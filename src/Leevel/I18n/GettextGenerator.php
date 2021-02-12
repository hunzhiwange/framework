<?php

declare(strict_types=1);

namespace Leevel\I18n;

use Gettext\Scanner\PhpScanner;
use Gettext\Generator\PoGenerator;
use Gettext\Translations;
use Gettext\Generator\Generator;
use Gettext\Generator\MoGenerator;
use Gettext\Loader\Loader;
use Gettext\Loader\MoLoader;
use Gettext\Loader\PoLoader;
use Gettext\Scanner\FunctionsScannerInterface;
use Gettext\Scanner\PhpFunctionsScanner;
use Leevel\Filesystem\Helper\create_directory;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use function Leevel\Filesystem\Helper\create_directory;

/**
 * 生成语言文件.
 */
class GettextGenerator
{
    /**
     * 生成 PO 文件数据.
     */
    public function generatorPoFiles(array $fileNames, array $languages, string $outputDir): array
    {
        return $this->generatorGettextFiles($fileNames, $languages, $outputDir, true);
    }

    /**
     * 生成 Mo 文件数据.
     */
    public function generatorMoFiles(array $fileNames, array $languages, string $outputDir): array
    {
        return $this->generatorGettextFiles($fileNames, $languages, $outputDir, false);
    }

    protected function generatorGettextFiles(
        array $fileNames, 
        array $languages,
        string $outputDir,
        bool $isTypePo, 
    ): array
    {
        $generatedLanguageFiles = [];
        foreach ($languages as $language) {
            foreach ($fileNames as $namespace => $dirs) {
                $generatedLanguageFiles[$namespace][] = $this->generatorGettextFile(
                    $language, 
                    $namespace, 
                    $dirs,
                    $outputDir,
                    $isTypePo, 
                );
            }
        }

        return $generatedLanguageFiles;
    }

    protected function generatorGettextFile(
        string $language,
        string $namespace,
        array $dirs,
        string $outputDir,
        bool $isTypePo,
    ): string
    {
        $generatedLanguageFile = $outputDir."/{$language}/{$namespace}.".($isTypePo ? 'po' : 'mo');
        $loader = $isTypePo ? new PoLoader() : new MoLoader();
        $generator = $isTypePo ? new PoGenerator() : new MoGenerator();
        $translations = $this->createTranslations($generatedLanguageFile, $language, $namespace, $loader);
        $phpScanner = $this->createPhpScanner($translations, $dirs);
        $this->generateFile($generator, $phpScanner, $generatedLanguageFile);

        return $generatedLanguageFile;
    }

    /**
     * @throws \RuntimeException
     */
    protected function generateFile(
        Generator $generator,
        PhpScanner $phpScanner,
        string $generatedLanguageFile,
    ): void
    { 
        $generatedLanguageDir = dirname($generatedLanguageFile);
        if (!is_dir($generatedLanguageDir)) {
            create_directory($generatedLanguageDir);
        }

        foreach ($phpScanner->getTranslations() as $translations) {
            if(false === $generator->generateFile($translations, $generatedLanguageFile)) {
                $message = sprintf('Failed to generate gettext file %s.', $generatedLanguageFile);
                throw new RuntimeException($message);
            }
        }
    }

    protected function createPhpScanner(Translations $translations, array $dirs): PhpScanner
    {
        $phpScanner = new class($translations) extends PhpScanner
        {
            public function getFunctionsScanner(): FunctionsScannerInterface
            {
                return new PhpFunctionsScanner(['gettext', '__']);
            }
        };
        $phpScanner->extractCommentsStartingWith('i18n:', 'Translators:');
        $phpScanner->addReferences(false);
        $this->scanFile($phpScanner, $dirs);

        return $phpScanner;
    }

    protected function scanFile(PhpScanner $phpScanner, array $dirs): void
    {
        $finder = (new Finder())
            ->in($dirs)
            ->name('*.php')
            ->sortByName()
            ->files();
        foreach ($finder as $file) {
            $phpScanner->scanFile($file->getRealPath());
        }
    }

    protected function createTranslations(
        string $generatedLanguageFile,
        string $language, 
        string $namespace, 
        Loader $loader
    ): Translations
    {
        $translations = Translations::create();
        $translations->setDescription($namespace);
        $translations->setLanguage($language);
        $this->setTranslationsHeaders($translations, $namespace);
        if (is_file($generatedLanguageFile)) {
            $translations = $this->loadTranslations($translations, $generatedLanguageFile, $loader);
        }

        return $translations;
    }

    protected function setTranslationsHeaders(Translations $translations, string $namespace): void
    {
        $headers = $translations->getHeaders();
        $headersData = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
            'MIME-Version' => '1.0',
            'Project-Id-Version' => $namespace,
            'POT-Creation-Date' => date('Y-m-d H:i:s'),
            'Last-Translator' =>  '',
            'Language-Team' => '',
        ];
        $headersData = array_merge($headersData, $headers->toArray());
        $headersData = array_merge($headersData, [
            'X-Generator' => 'php leevel i18n:generate',
            'PO-Revision-Date' => date('Y-m-d H:i:s'),
        ]);
        foreach ($headersData as $key => $value) {
            $headers->set($key, $value);
        }
    }

    protected function loadTranslations(
        Translations $translations, 
        string $generatedLanguageFile, 
        Loader $loader
    ): Translations
    {
        return $translations->mergeWith(
            $loader->loadFile($generatedLanguageFile)
        );
    }
}

// import fn.
class_exists(create_directory::class);
