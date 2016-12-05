<?php
use Migrations\AbstractMigration;

class CreateMessages extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('messages');
        $table->addColumn('id_user', 'integer', [
            'default' => null,
            'null' => false
        ]);
        $table->addColumn('message', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false
        ]);
        $table->addColumn('timestamp', 'string', [
            'default' => null,
            'null' => false
        ]);
        $table->create();
    }
}
