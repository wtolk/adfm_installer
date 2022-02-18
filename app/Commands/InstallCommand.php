<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;
use TitasGailius\Terminal\Terminal;

class InstallCommand extends Command
{
    protected $signature = 'install';
    protected $description = 'Устанавливает ларавел и adfm пакеты';
    protected $config;

    public function handle()
    {
        $this->config = Yaml::parseFile('config.yaml');
        $this->installLaravel();

        foreach ($this->config['packages'] as $package) {
            $response = Terminal::run('cd '.$this->config['path'].'; composer require '.$package);
            foreach ($response as $line) {
                $this->info($line);
            }
        }

        if (isset($this->config['publish'])) {
            foreach ($this->config['publish'] as $provider) {
                $response = Terminal::run('cd '.$this->config['path'].'; php artisan vendor:publish --provider="'.$provider.'"');
                foreach ($response as $line) {
                    $this->info($line);
                }
            }
        }

        $this->setUpDB();

        if ($this->config['npm']) {
            $response = Terminal::timeout(600)->run('cd '.$this->config['path'].'; npm install');
            foreach ($response as $line) {
                $this->info($line);
            }
            $response = Terminal::timeout(600)->run('cd '.$this->config['path'].'; npm run dev');
            foreach ($response as $line) {
                $this->info($line);
            }
        }

        $this->info("Установка закончена");
    }

    /**
     * Устанавливаем свежий ларавел
     * @return void
     */
    private function installLaravel()
    {
        $response = Terminal::run('composer create-project laravel/laravel '.$this->config['path'].' v8.6.11');
        foreach ($response as $line) {
            $this->info($line);
        }
    }

    /**
     * Устанавливает подключение к базе данных
     * @return void
     */
    private function setUpDB()
    {
        if (isset($this->config['DB'])) {
            foreach ($this->config['DB'] as $key => $value) {
                $response = Terminal::run('cd '.$this->config['path'].'; php artisan adfm:set_env '.$key.' '.$value);
                foreach ($response as $line) {
                    $this->info($line);
                }
            }
        }

        if ($this->config['migrate'] == true) {
            $response = Terminal::run('cd '.$this->config['path'].'; php artisan migrate');
            foreach ($response as $line) {
                $this->info($line);
            }
        }
    }
}
