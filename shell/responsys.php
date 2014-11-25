<?php
require_once 'abstract.php';

class Mage_Shell_Responsys extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        try {
            if ($this->getArg('sync')) {
                Mage::getModel('responsys/api')->syncCustomers();
            }
            elseif ($this->getArg('welcome')) {
                Mage::getModel('responsys/api')->sendWelcome();
            }
            else {
                echo $this->usageHelp();
            }
        }
        catch (Exception $e) {
            echo "There was an error: {$e->getMessage()}\n";
        }
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f responsys.php -- [options]

  --sync            Sync new customer data with Responsys
  --welcome         Trigger welcome event
  help              This help

USAGE;
    }
}

$shell = new Mage_Shell_Responsys();
$shell->run();