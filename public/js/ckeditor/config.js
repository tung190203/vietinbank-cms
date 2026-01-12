/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    // Simplify the dialog windows.
    config.extraPlugins = 'youtube';
    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.filebrowserBrowseUrl = baseUrl + '/ckfinder/browser';
    config.filebrowserImageBrowseUrl = baseUrl + '/ckfinder/browser?type=Images';
};
