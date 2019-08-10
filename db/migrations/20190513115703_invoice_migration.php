<?php

use Phinx\Migration\AbstractMigration;

class InvoiceMigration extends AbstractMigration
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
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('invoice', ['commnet' => '發票資料表', 'collation' => 'utf8mb4_unicode_ci'])
             ->addColumn('invoice_number', 'string', 
                        ['comment' => '發票號碼', 'limit' => 10])
             ->addColumn('amount', 'integer', 
                        ['comment' => '金額'])
             ->addColumn('invoice_date', 'date', 
                        ['comment' => '發票日期'])
             ->addColumn('created_at', 'timestamp', 
                        ['comment' => '建立時間',
                         'default' => 'CURRENT_TIMESTAMP',
                         'update'  => ''])
             ->create();
    }
}
