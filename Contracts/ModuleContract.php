<?php

namespace Vitlabs\Modules\Contracts;

interface ModuleContract {

    // Main methods
    function __construct($path, $parsedJSON);
    function getPath();

    // Name
    function getName();
    function setName($name);

    // Installed
    function isInstalled();
    function setInstalled($installed);

    // Providers
    function getProviders();
    function setProviders(array $providers);

    // Files
    function getFiles();
    function setFiles(array $files);

    // Installer
    function getInstaller();
    function setInstaller($installer);

    // Uninstaller
    function getUninstaller();
    function setUninstaller($uninstaller);

    // Other
    function getParsedJSON();
    function save();

}