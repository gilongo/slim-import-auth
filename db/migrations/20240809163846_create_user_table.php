<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('firstName', 'string', ['limit' => 255])
            ->addColumn('lastName', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('username', 'string', ['limit' => 100])
            ->addColumn('password', 'string', ['limit' => 40])
            ->addColumn('birthday', 'date')
            ->addIndex(['username','email'], ['unique' => true])
            ->create();

        $table->saveData([
            [
                'firstName' => 'Lorenzo',
                'lastName' => 'D\'Ianni',
                'email' => 'ld@qwentes.it',
                'username' => 'lorenzod',
                'password' => '$fV1v!-_er',
                'birthday' => '1991-03-22'
            ],
            [
                'firstName' => 'Matteo',
                'lastName' => 'Barone',
                'email' => 'ld@qwentes.it',
                'username' => 'matteob',
                'password' => 'P1pp0-Sal',
                'birthday' => '1990-04-02'
            ],
        ]);
    }
}
