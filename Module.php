<?php

namespace Vitlabs\Modules;

use File;
use ReflectionClass;
use Exception;
use Vitlabs\Modules\Contracts\ModuleContract;
use Vitlabs\Modules\Exceptions\ModuleJSONException;

class Module implements ModuleContract {

    protected $path;
    protected $name;
    protected $installed;
    protected $providers;
    protected $files;
    protected $installer;
    protected $uninstaller;
    protected $json;

    // Main methods
    public function __construct($path, $parsedJSON)
    {
        // Set JSON content
        $this->json = $parsedJSON;

        // Set path
        $this->path = $path;
        $this->validatePath();

        // Set name
        $this->name = $this->getProperty('name');
        $this->validateName();

        // Set installed
        $this->installed = $this->getProperty('installed');
        $this->validateInstalled();

        // Set providers
        $this->providers = $this->getProperty('providers');
        $this->validateProviders();

        // Set files
        $this->files = $this->getProperty('files');
        $this->validateFiles();

        // Set installer
        $this->installer = $this->getProperty('installer');
        $this->validateInstaller();

        // Set uninstaller
        $this->uninstaller = $this->getProperty('uninstaller');
        $this->validateUninstaller();
    }

    public function getPath()
    {
        return $this->path;
    }

    // Name
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->validateName();
        $this->setProperty('name');
        return $this;
    }

    // Installed
    public function isInstalled()
    {
        return $this->installed;
    }

    public function setInstalled($installed)
    {
        $this->installed = boolval($installed);
        $this->validateInstalled();
        $this->setProperty('installed');
        return $this;
    }

    // Providers
    public function getProviders()
    {
        return $this->providers;
    }

    public function setProviders(array $providers)
    {
        $this->providers = $providers;
        $this->validateProviders();
        $this->setProperty('providers');
        return $this;
    }

    // Files
    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;
        $this->validateFiles();
        $this->setProperty('files');
        return $this;
    }

    // Installer
    public function getInstaller()
    {
        return $this->installer;
    }

    public function setInstaller($installer)
    {
        $this->installer = $installer;
        $this->validateInstaller();
        $this->setProperty('installer');
        return $this;
    }

    // Uninstaller
    public function getUninstaller()
    {
        return $this->uninstaller;
    }

    public function setUninstaller($uninstaller)
    {
        $this->uninstaller = $uninstaller;
        $this->validateUninstaller();
        $this->setProperty('uninstaller');
        return $this;
    }

    // Other
    public function getParsedJSON()
    {
        return $this->json;
    }

    public function save()
    {
        // Validate
        $this->validateAll();

        // Save "module.json" file
        return !(File::put($this->path . '/module.json', $this->makeJSONContent(), true) === FALSE);
    }

    /* Helper methods */
    protected function makeJSONContent()
    {
        return json_encode($this->json, JSON_PRETTY_PRINT);
    }

    protected function getProperty($property)
    {
        // Check if property is set
        if ( ! isset($this->json->{$property}))
        {
            return null;
        }

        // Get property value
        $value = $this->json->{$property};

        // Return value
        return $value;
    }

    protected function setProperty($property)
    {
        $this->json->{$property} = $this->{$property};
    }

    protected function error($message = null)
    {
        throw new ModuleJSONException($message . " Module [$this->path].");
    }

    /* Check classes */
    protected function checkClass($class)
    {
        if ( ! class_exists($class))
        {
            $this->error("Class [$class] does not exist.");
        }
    }

    protected function checkClasses(array $classes)
    {
        foreach ($classes as $class)
        {
            $this->checkClass($class);
        }
    }

    protected function checkImplements($className, $interface)
    {
        $class = new ReflectionClass($className);

        if ( ! $class->implementsInterface($interface))
        {
            $this->error("Class [$className] must implement [$interface] interface.");
        }
    }

    protected function checkExtends($class, $parent)
    {
        if ( ! is_subclass_of($class, $parent))
        {
            $this->error("Class [$class] must extends [$parent] class.");
        }
    }

    /* Check files */
    protected function checkFile($file)
    {
        if ( ! File::isFile($file))
        {
            $this->error("File [$file] does not exist.");
        }
    }

    protected function checkFiles(array $files)
    {
        foreach ($files as $file)
        {
            $this->checkFile($file);
        }
    }

    /* Validation */
    protected function validatePath()
    {
        if ( ! File::isDirectory($this->path)) $this->error("Module path is not a directory.");
        if ( ! File::isWritable($this->path . '/module.json')) $this->error('File [module.json] is not writable.');
    }

    protected function validateName()
    {
        if ( ! is_string($this->name)) $this->error('Property [name] should be a string.');
    }

    protected function validateInstalled()
    {
        if ( ! is_bool($this->installed)) $this->error('Property [installed] should be a boolean.');
    }

    protected function validateProviders()
    {
        if ($this->providers != null && ! is_array($this->providers)) $this->error('Property [providers] should be an array.');
        if (is_array($this->providers))
        {
            $this->checkClasses($this->providers);

            foreach ($this->providers as $provider)
            {
                $this->checkExtends($provider, 'Illuminate\Support\ServiceProvider');
            }
        }
    }

    protected function validateFiles()
    {
        if ($this->files != null && ! is_array($this->files)) $this->error('Property [files] should be an array.');
        if (is_array($this->files)) $this->checkFiles($this->files);
    }

    protected function validateInstaller()
    {
        if ($this->installer != '')
        {
            $fragments = explode('::', $this->installer, 2);
            $this->checkImplements($fragments[0], 'Vitlabs\Modules\Contracts\InstallerContract');
        }
    }

    protected function validateUninstaller()
    {
        if ($this->uninstaller != '')
        {
            $fragments = explode('::', $this->uninstaller, 2);
            $this->checkImplements($fragments[0], 'Vitlabs\Modules\Contracts\UninstallerContract');
        }
    }

    protected function validateAll()
    {
        $this->validatePath();
        $this->validateName();
        $this->validateInstalled();
        $this->validateProviders();
        $this->validateFiles();
        $this->validateInstaller();
        $this->validateUninstaller();
    }

}