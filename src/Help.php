<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli;

use Aura\Cli\Context\GetoptParser;

/**
 * 
 * Represents the "help" information for a command.
 * 
 * @package Aura.Cli
 * 
 */
class Help
{
    /**
     * 
     * The long-form help text.
     * 
     * @var string
     * 
     */
    protected $descr;

    /**
     * 
     * A getopt parser.
     * 
     * @var GetoptParser
     * 
     */
    protected $getopt_parser = array();

    /**
     * 
     * The option definitions.
     * 
     * @var array
     * 
     */
    protected $options = array();

    /**
     * 
     * A single-line summary for the command.
     * 
     * @var string
     * 
     */
    protected $summary;

    /**
     * 
     * One or more single-line usage examples.
     * 
     * @var string|array
     * 
     */
    protected $usage;

    /**
     * 
     * Constructor.
     * 
     * @param GetoptParser $getopt_parser A getopt parser.
     * 
     */
    public function __construct(GetoptParser $getopt_parser)
    {
        $this->getopt_parser = $getopt_parser;
        $this->init();
    }

    /**
     * 
     * Use this to initialize the help object in child classes.
     * 
     * @return null
     * 
     */
    protected function init()
    {
    }

    /**
     * 
     * Sets the option definitions.
     * 
     * @param array $options The option definitions.
     * 
     * @return null
     * 
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * 
     * Gets the option definitions.
     * 
     * @return array
     * 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 
     * Sets the single-line summary.
     * 
     * @param string $summary The single-line summary.
     * 
     * @return null
     * 
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * 
     * Gets the single-line summary.
     * 
     * @return string
     * 
     */
    public function getSummary($summary)
    {
        return $this->summary;
    }
    
    /**
     * 
     * Sets the usage line(s).
     * 
     * @param string|array $usage The usage line(s).
     * 
     * @return null
     * 
     */
    public function setUsage($usage)
    {
        $this->usage = (array) $usage;
    }

    /**
     * 
     * Sets the long-form description.
     * 
     * @param string $descr The long-form description.
     * 
     * @return null
     * 
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
    }

    /**
     * 
     * Gets the formatted help output.
     * 
     * @param string $name The command name.
     * 
     * @return string
     * 
     */
    public function getHelp($name)
    {
        $help = $this->getHelpSummary($name)
              . $this->getHelpUsage($name)
              . $this->getHelpDescr()
              . $this->getHelpOptions()
        ;

        if (! $help) {
            $help = "No help available.";
        }

        return rtrim($help) . PHP_EOL;
    }

    /**
     * 
     * Gets the formatted summary output.
     * 
     * @param string $name The command name.
     * 
     * @return string
     * 
     */
    protected function getHelpSummary($name)
    {
        if (! $this->summary) {
            return;
        }

        return "<<bold>>SUMMARY<<reset>>" . PHP_EOL
             . "    <<bold>>{$name}<<reset>> -- {$this->summary}"
             . PHP_EOL . PHP_EOL;
    }

    /**
     * 
     * Gets the formatted usage output.
     * 
     * @param string $name The command name.
     * 
     * @return string
     * 
     */
    protected function getHelpUsage($name)
    {
        if (! $this->usage) {
            return;
        }

        $text = "<<bold>>USAGE<<reset>>" . PHP_EOL;
        foreach ($this->usage as $usage) {
             $text .= "    <<ul>>$name<<reset>> {$usage}" . PHP_EOL;
        }
        return $text . PHP_EOL;
    }

    /**
     * 
     * Gets the formatted options output.
     * 
     * @return string
     * 
     */
    protected function getHelpOptions()
    {
        if (! $this->options) {
            return;
        }
        
        $text = "<<bold>>OPTIONS<<reset>>" . PHP_EOL;
        foreach ($this->options as $string => $descr) {
            $option = $this->getopt_parser->newOption($string, $descr);
            $text .= $this->getHelpOption($option). PHP_EOL;
        }
        return $text;
    }

    /**
     * 
     * Gets the formatted output for one option.
     * 
     * @param StdClass An option struct.
     * 
     * @return string
     * 
     */
    protected function getHelpOption($option)
    {
        $text = "    "
              . $this->getHelpOptionParam($option->name, $option->param, $option->multi)
              . PHP_EOL;

        if ($option->alias) {
            $text .= "    "
                   . $this->getHelpOptionParam($option->alias, $option->param, $option->multi)
                   . PHP_EOL;
        }

        if (! $option->descr) {
            $option->descr = 'No description.';
        }
        
        return $text
             . "        " . trim($option->descr) . PHP_EOL;
    }

    /**
     * 
     * Gets the formatted output for an option param.
     * 
     * @param string $name The option name.
     * 
     * @param string $param The option param flag.
     * 
     * @param bool $multi The option multi flag.
     * 
     * @return string
     * 
     */
    protected function getHelpOptionParam($name, $param, $multi)
    {
        $text = "{$name}";
        if (strlen($name) == 2) {
            $text .= $this->getHelpOptionParamShort($name, $param);
        } else {
            $text .= $this->getHelpOptionParamLong($name, $param);
        }

        if ($multi) {
            $text .= " [$text [...]]";
        }
        return $text;
    }

    /**
     * 
     * Gets the formatted output for a short option param.
     * 
     * @param string $name The option name.
     * 
     * @param string $param The option param flag.
     * 
     * @return string
     * 
     */
    protected function getHelpOptionParamShort($name, $param)
    {
        if ($param == 'required') {
            return " <value>";
        }

        if ($param == 'optional') {
            return " [<value>]";
        }
    }

    /**
     * 
     * Gets the formatted output for a long option param.
     * 
     * @param string $name The option name.
     * 
     * @param string $param The option param flag.
     * 
     * @return string
     * 
     */
    protected function getHelpOptionParamLong($name, $param)
    {
        if ($param == 'required') {
            return "=<value>";
        }

        if ($param == 'optional') {
            return "[=<value>]";
        }
    }

    /**
     * 
     * Gets the formatted output for the long-form description.
     * 
     * @return string
     * 
     */
    public function getHelpDescr()
    {
        if (! $this->descr) {
            return;
        }

        return "<<bold>>DESCRIPTION<<reset>>" . PHP_EOL
             . "    " . trim($this->descr) . PHP_EOL . PHP_EOL;
    }

}
