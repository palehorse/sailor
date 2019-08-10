<?php

use Phinx\Migration\AbstractMigration;

class CreateRequestLog extends AbstractMigration
{
    public function change()
    {
        $this->table('request_log')
             ->addColumn('source', 'string', [
                 'limit' => 32,
             ])
             ->addColumn('content', 'text', [
                 'comment' => '請求內容',
             ])
             ->addColumn('created_at', 'timestamp', 
                        ['comment' => '建立時間',
                         'default' => 'CURRENT_TIMESTAMP',
                         'update'  => ''])
             ->create();
    }
}
