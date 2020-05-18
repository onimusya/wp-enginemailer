<?php

class Twig 
{
	/**
	 * @var array Paths to Twig templates
	 */
	private $paths = [];
	/**
	 * @var array Twig Environment Options
	 * @see http://twig.sensiolabs.org/doc/api.html#environment-options
	 */
	private $config = [];
	/**
	 * @var array Functions to add to Twig
	 */
	private $functions_asis = [
		'get_permalink', 'settings_fields', 'wp_editor', '__', 'wp_login_form', 'wp_lostpassword_url', ' wp_login_url'
	];
	/**
	 * @var array Functions with `is_safe` option
	 * @see http://twig.sensiolabs.org/doc/advanced.html#automatic-escaping
	 */
	private $functions_safe = [

	];
	/**
	 * @var bool Whether functions are added or not
	 */
	private $functions_added = FALSE;
	/**
	 * @var Twig_Environment
	 */
	private $twig;
	/**
	 * @var Twig_Loader_Filesystem
	 */
	private $loader;

	public function __construct($params = [])
	{
		if (isset($params['functions'])) {
			$this->functions_asis =
				array_unique(
					array_merge($this->functions_asis, $params['functions'])
				);
			unset($params['functions']);
		}

		if (isset($params['functions_safe'])) {
			$this->functions_safe =
				array_unique(
					array_merge($this->functions_safe, $params['functions_safe'])
				);
			unset($params['functions_safe']);
		} 

		if (isset($params['paths'])) {
			$this->paths = $params['paths'];
			unset($params['paths']);
		} else {
			$this->paths = EM_PATH . 'templates';
		}

		// default Twig config
		if (isset($params['cache'])) {
			$this->config['cache'] = $params['cache'];
			unset($params['cache']);
		} else {
			$this->config['cache'] = EM_PATH . 'cache';
		}
        
        $this->config['debug'] = FALSE;
        $this->config['autoescape'] = 'html';
    		
        $this->config = array_merge($this->config, $params);

        //log_message("info","Template Path: " . $this->paths);
        //log_message("info","Cache Path: " . $this->config['cache']);

	}


	protected function resetTwig()
	{
		$this->twig = null;
		$this->createTwig();
	}

	protected function createTwig()
	{
		// $this->twig is singleton
		if ($this->twig !== null)
		{
			return;
		}
		if ($this->loader === null)
		{
            //$this->loader = new \Twig_Loader_Filesystem($this->paths);
            $this->loader = new \Twig\Loader\FilesystemLoader($this->paths);
		}
        //$twig = new \Twig_Environment($this->loader, $this->config);
        $twig = new \Twig\Environment($this->loader, $this->config);
        
		if ($this->config['debug'])
		{
			$twig->addExtension(new \Twig\Extension\DebugExtension());
		}

		$twig->addExtension(new \Twig\Extension\StringLoaderExtension());
		
		$this->twig = $twig;
	}

	protected function setLoader($loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Registers a Global
	 *
	 * @param string $name  The global name
	 * @param mixed  $value The global value
	 */
	public function addGlobal($name, $value)
	{
		$this->createTwig();
		$this->twig->addGlobal($name, $value);
	}

	/**
	 * Renders Twig Template and Returns as String
	 *
	 * @param string $view   Template filename without `.twig`
	 * @param array  $params Array of parameters to pass to the template
	 * @return string
	 */
	public function render($view, $params = [])
	{
		$this->createTwig();
		$this->addFunctions();
		//$view = $view . '.twig';
		return $this->twig->render($view, $params);
	}
    
	protected function addFunctions()
	{
		//log_message('debug', 'Twig->addFunctions(): enter');
		// Runs only once
		if ($this->functions_added)
		{
			return;
		}
		// as is functions
		foreach ($this->functions_asis as $function)
		{
			//log_message('debug', 'Twig->addFunctions(): checking function ' . $function);
			if (function_exists($function))
			{
				//log_message('debug', 'Twig->addFunctions(): ' . $function);
				$this->twig->addFunction(
					new \Twig\TwigFunction(
						$function,
						$function
					)
				);
			}
		}
		// safe functions
		foreach ($this->functions_safe as $function)
		{
			if (function_exists($function))
			{
				$this->twig->addFunction(
					new \Twig\TwigFunction(
						$function,
						$function,
						['is_safe' => ['html']]
					)
				);
			}
		}
		// customized functions
		if (function_exists('anchor'))
		{
			$this->twig->addFunction(
				new \Twig\TwigFunction(
					'anchor',
					[$this, 'safe_anchor'],
					['is_safe' => ['html']]
				)
			);
		}
		$this->functions_added = TRUE;
	}
	/**
	 * @param string $uri
	 * @param string $title
	 * @param array  $attributes [changed] only array is acceptable
	 * @return string
	 */
	public function safe_anchor($uri = '', $title = '', $attributes = [])
	{
		$uri = html_escape($uri);
		$title = html_escape($title);
		$new_attr = [];
		foreach ($attributes as $key => $val)
		{
			$new_attr[html_escape($key)] = html_escape($val);
		}
		return anchor($uri, $title, $new_attr);
	}
	/**
	 * @return \Twig_Environment
	 */
	public function getTwig()
	{
		$this->createTwig();
		return $this->twig;
	}    
}
