<?php
test('Проверяем что установка прошла успешна', function () {
    $this->artisan('install')
        ->expectsOutput('Установка закончена')
        ->assertExitCode(0);

    $this->assertDirectoryExists('test-instance/node_modules');
});
