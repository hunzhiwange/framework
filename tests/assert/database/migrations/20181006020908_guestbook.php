<?php


use Phinx\Migration\AbstractMigration;

class Guestbook extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $guestbook = $this->table('guestbook');
        $guestbook->addColumn('name', 'string', ['limit'=>64]);
        $guestbook->addColumn('content', 'text', ['default'=> '', 'comment'=>'评论内容']);
        $guestbook->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间']);
        $guestbook->save();
    }
}
