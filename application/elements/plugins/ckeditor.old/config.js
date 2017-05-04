/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  // Define changes to default configuration here.
  // For the complete reference:
  // http://docs.ckeditor.com/#!/api/CKEDITOR.config

  config.language = 'en';
  
  config.width = 720;
  
  config.enterMode = CKEDITOR.ENTER_BR;
  config.contentsCss = SITE_URL + 'application/elements/css/fonts.css';
  
  config.filebrowserBrowseUrl = SITE_URL + 'application/elements/plugins/kcfinder/browse.php?type=files';
  config.filebrowserImageBrowseUrl = SITE_URL + 'application/elements/plugins/kcfinder/browse.php?type=images';
  config.filebrowserFlashBrowseUrl = SITE_URL + 'application/elements/plugins/kcfinder/browse.php?type=flash';
  config.filebrowserUploadUrl = SITE_URL + 'application/elements/plugins/kcfinder/upload.php?type=files';
  config.filebrowserImageUploadUrl = SITE_URL + 'application/elements/plugins/kcfinder/upload.php?type=images';
  config.filebrowserFlashUploadUrl = SITE_URL + 'application/elements/plugins/kcfinder/upload.php?type=flash';
  config.format_tags =  'p;h2;h3;h4;h5;h6;div';
  config.toolbar = 'Basic';
  config.toolbarCanCollapse = false;

  config.pasteFromWordPromptCleanup = true;
  config.pasteFromWordRemoveFontStyles = true;
  config.forcePasteAsPlainText = true;
  config.ignoreEmptyParagraph = true;
  config.removeFormatAttributes = true;
  
  // The toolbar groups arrangement, optimized for two toolbar rows.
  config.toolbarGroups = [
    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'links' },
    { name: 'insert' },
    { name: 'forms' },
    { name: 'tools' },
    { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
    { name: 'others' },
    '/',
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
    { name: 'styles' },
    { name: 'colors' },
    { name: 'about' }
  ];
  
  config.toolbar_Basic =
  [
    { name: 'clipboard', items : [ 'Undo','Redo' ] },
    { name: 'editing', items:  [ 'Bold','Italic','Underline' ] },
    { name: 'styles', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','NumberedList','BulletedList' ] },
    { name: 'insert', items : [ 'Image', 'Link','Unlink','Table'] },
    { name: 'insert', items : [ 'FontSize','TextColor','BGColor' ] },
    { name: 'others', items : [ 'RemoveFormat','Source' ] }
  ];

  // Remove some buttons, provided by the standard plugins, which we don't
  // need to have in the Standard(s) toolbar.
  config.removeButtons = 'Subscript,Superscript';
  
  config.extraPlugins = 'justify';
  // Color Dependencies
  config.extraPlugins = 'panelbutton';
  config.extraPlugins = 'floatpanel';
  // Font Dependencies
  config.extraPlugins = 'button';
  config.extraPlugins = 'panel';
  config.extraPlugins = 'listblock';
  config.extraPlugins = 'richcombo';
  // Actual Plugins
  config.extraPlugins = 'font,colorbutton';
  config.extraPlugins = 'colorbutton';

  // Se the most common block elements.
  config.format_tags = 'p;h1;h2;h3;pre';

  // Make dialogs simpler.
  config.removeDialogTabs = 'image:advanced;link:advanced';
};
