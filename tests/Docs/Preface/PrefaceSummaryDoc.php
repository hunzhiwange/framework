<?php

declare(strict_types=1);

namespace Tests\Docs\Preface;

/**
 * @api(
 *     title="Summary",
 *     zh-CN:title="概述",
 *     zh-TW:title="概述",
 *     path="README",
 *     description="再小的个体都有自己的品牌，QueryPHP 与你相约这个时代。",
 *     zh-CN:description="再小的个体都有自己的品牌，QueryPHP 与你相约这个时代。",
 *     zh-TW:description="再小的个体都有自己的品牌，QueryPHP 与你相约这个时代。",
 * )
 */
class PrefaceSummaryDoc
{
    /**
     * @api(
     *     zh-CN:title="概览",
     *     zh-CN:description="
     * QueryPHP 文档系统概览。
     *
     * 项目  |  标识 |  备注
     * --   |---|--
     * 序言  |  preface |
     * 入门  | started  |
     * 指南  |  guide |
     * 架构  | architecture  |
     * 路由  |  routing |
     * 模板  | template  |
     * 数据库  |  database |
     * ORM  | orm  |
     * 验证  | validate  |
     * Swoole  | Protocol  |
     * 组件  |  component |
     * 测试  | test  |
     * 开发者 | developer |
     * ",
     *     zh-CN:note="",
     *     lang="shell",
     * )
     */
    public function doc1(): void
    {
    }
}
