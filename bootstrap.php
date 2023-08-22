<?php

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

require 'model/UserModel.php';
require 'controller/UserController.php';
require_once 'controller/uploadImage.php';
require 'model/documentModel.php';
require 'controller/documentController.php';
require 'model/templateModel.php';
require 'controller/templateController.php';
require 'database.php';
