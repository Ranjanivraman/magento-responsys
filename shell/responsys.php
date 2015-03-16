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
            if ($arg = $this->getArg('sync')) {
                switch ($arg) {
                    case 'customers':
                        Mage::getResourceModel('responsys/customer_collection')->sync();
                        break;
                    case 'products':
                        Mage::getResourceModel('responsys/product_collection')->sync();
                        break;
                    default:
                        Mage::getResourceModel('responsys/customer_collection')->sync();
                        Mage::getResourceModel('responsys/product_collection')->sync();
                        break;
                }
            }
            elseif ($arg = $this->getArg('event')) {
                Mage::getResourceModel('responsys/customer_collection')->event($arg);
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

  --sync    <entity>        Sync given data type with Responsys
  --event   <event_name>    Trigger given event in Responsys
  help                      This help

USAGE;
    }
}

$shell = new Mage_Shell_Responsys();
$shell->run();