<?php

include './vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public $config;


    function start()
    {
        $this->config = Yaml::parseFile('config.yaml');
        $this->installLaravel();


        foreach ($this->config['packages'] as $package) {
            $this->_exec('cd '.$this->config['path'].'; composer require '.$package);
        }


        $this->setUpDB();

        if ($this->config['npm']) {
            $this->_exec('cd '.$this->config['path'].'; npm install');
            $this->_exec('cd '.$this->config['path'].'; npm run dev');
        }

        $this->say("Установка закончена");
    }

    private function installLaravel()
    {
        $this->_exec('composer create-project laravel/laravel '.$this->config['path']);
    }

    private function setUpDB()
    {
        if (isset($this->config['DB'])) {
            foreach ($this->config['DB'] as $key => $value) {
                $this->_exec('cd '.$this->config['path'].'; php artisan adfm:set_env '.$key.' '.$value);
            }
        }
    }

}
