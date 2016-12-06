<?php
use Migrations\AbstractMigration;

class CreateSessions extends AbstractMigration
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
        $table = $this->table('sessions');
        $table->addColumn('user_id', 'integer', [
            'default' => null,
            'null' => false
        ]);
        $table->addColumn('timestamp', 'string', [
            'default' => null,
            'null' => false
        ]);
        $table->addColumn('apikey','string', [
            'default' => null,
            'null' => false
        ]);
        $table->create();
    }
}
