<?php

/**
 * @defgroup plugins_generic_easyFileApi  Easy to Use File API to download files
 */

/**
 * @file plugins/generic/easyFileApi/index.php
 *
 * Copyright (c) 2023 Thomas F. K. Jorna
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_easyFileApi
 * @brief Wrapper for EasyFileApi plugin
 *
 */

require_once('EasyFileApiPlugin.inc.php');

return new EasyFileApiPlugin();