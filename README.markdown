sfThemePlugin
=============

This plugin allows for themes to be used with your symfony project. A
theme is a very simple idea and consists of:

 * a layout
 * a collection of stylesheets
 * a collection of javascripts

By defining several themes, you can:

 * Have different themes on an action-by-action, module-by-module, or even
   route-by-route basis.

 * Easily change the entire look of your site for promotional or seasonal reasons

 * Switch between themes on the fly using the web debug toolbar

Using the plugin couldn't be easier and consists of two steps

 * Create a theme or themes
 * Set each theme globally or exactly where you need it

Creating themes
---------------

As mentioned, a theme consists of a layout, stylesheets, and javascript.
To create a theme, simply define each of these in `app.yml`:

    all:
      theme:
        themes:
          new_design:
            layout:       new_design
            stylesheets:  [main, print: { media: print }]
            javascripts:  []

The layout can exist in the `templates` directory of your application or in
the `templates` directory of any plugin. This means that your entire
theme can live inside a plugin and be repackaged for other projects.

Setting Themes
--------------

Once you've created a theme or themes, you'll want to instruct your application
to use those themes. By default, the plugin won't start using themes until
you tell it to. So, you can apply theming selectively without affecting
the rest of your application.

Themes can be attached in many different ways:

### Using a global theme

Very commonly, you'll want to use one theme for your entire application.
This basically replaces the use of `view.yml` to define the layout,
stylesheets, and javascripts for your application. Defining a default
theme is easy:

    all:
      theme:
        controller_options:
          default_theme:   new_design

By doing this, the `new_design` theme will be used globally in your application.

### Setting a theme in the action

The global default theme can easily be overridden in any action. For example,
suppose that you still need to use the `old_design` theme for a particular action:

    public function executeIndex(sfWebRequest $request)
    {
      // ...
      
      $this->loadTheme('old_design');
    }

This method is the "strongest" method of setting a theme and will override
all other methods.

### Setting a theme for a module

Alternatively, themes can be set on a module-by-module basis. Once again,
this will override the global default theme. For example, suppose the module
`old_module` should use the theme `old_design`:

    all:
      theme:
        controller_options:
          modules:
            old_module:    old_design

### Setting a theme for a route

You can even set a theme for a paricular route. This means that you could
have the same content served by two different urls, each rendering their
own theme! For example, suppose that you create a route called `homepage_old`,
and you want it to use the `old_design` theme:

    all:
      theme:
        controller_options:
          routes:
            homepage_old:    old_design

### Switching themes based on a url parameter `?sf_theme=`

By default (this can be turned off), you can switch to any available theme
by appending `?sf_theme=theme_name` to the end of any url.

This will set a user attribute so that you can browse your entire site
with this theme. To unset the user attribute, simply append `?sf_theme=`
to the end of your url (without any theme name).

### Advanced theming using an event

If you need to be more specific in your theming, you can easily do this
by connecting to the `theme.set_theme_from_request` event. For example,
suppose you want to use a different theme for users with the `fr` culture.
In your application configuration file (`apps/app_name/config/app_nameConfiguration.class.php`):

    public function configure()
    {
      $this->dispatcher->connect(
        'theme.set_theme_from_request',
        array($this, 'listenSetThemeFromRequest')
      );
    }

    public function listenSetThemeFromRequest(sfEvent $event)
    {
      $context = $event['context'];
      
      if ($context->getUser()->getCulture() == 'fr')
      {
        $event->setReturnValue('fr_theme');
        
        return true;
      }
      
      return false;
    }

The Fine Details
----------------

This plugin was taken from [sympal CMF](http://www.sympalphp.org) and was
developed by both Jon Wage and Ryan Weaver.

If you have questions, comments or anything else, email me at ryan [at] thatsquality.com











