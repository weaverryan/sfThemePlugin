sfThemePlugin
=============

This plugin allows for themes to be used with your symfony project. A
theme is a very simple idea and consists of:

 * a layout
 * a collection of stylesheets
 * a collection of javascripts

By defining several themes, you can:

 * Have different themes on an action-by-action basis
 * Easily change the entire look of your site for promotional or seasonal reasons

Usage
-----

Using the plugin couldn't be easier. First, define a default theme in
`app.yml`:

    all:
      theme:
        default_theme:   new_design

By doing this, the `new_design` theme will be used globally in your application.
This can easily be overridden in any action. For example, suppose that you
still need to use the `old_design` theme for a particular action:

    public function executeIndex(sfWebRequest $request)
    {
      // ...
      
      $this->setTheme('old_design');
    }

Alternatively, themes can be set on a module-by-module basis or even
a route-by-route basis in `app.yml`. For example, suppose the module
`old_module` and the route `old_route` both should use the theme `old_design`:

    all:
      theme:
        default_theme:   new_design

        modules:
          old_module:    old_design

Configuration
-------------

As mentioned, a theme is simply a layout, stylesheets, and javascript. So,
configuring a theme consists of defining each of these components in `app.yml`:

    all:
      theme:
        themes:
          new_design:
            layout:       new_design
            stylesheets:  [main, print: { media: print }]
            javascripts:  []

The layout can exist in the `templates` directory of your application or in
the `templates` directory of any application. This means that your entire
theme can live inside a plugin and be repackaged for other projects.

