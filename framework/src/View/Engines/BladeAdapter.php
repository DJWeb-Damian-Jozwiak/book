<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Engines;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Utils\File;
use DJWeb\Framework\View\AssetManager;
use DJWeb\Framework\View\Contracts\RendererContract;
use DJWeb\Framework\View\Directives\ComponentDirective;
use DJWeb\Framework\View\Directives\DoWhileDirective;
use DJWeb\Framework\View\Directives\EmptyDirective;
use DJWeb\Framework\View\Directives\ExtendDirective;
use DJWeb\Framework\View\Directives\ForDirective;
use DJWeb\Framework\View\Directives\ForeachDirective;
use DJWeb\Framework\View\Directives\IfDirective;
use DJWeb\Framework\View\Directives\IssetDirective;
use DJWeb\Framework\View\Directives\SectionDirective;
use DJWeb\Framework\View\Directives\SlotDirective;
use DJWeb\Framework\View\Directives\StackDirective;
use DJWeb\Framework\View\Directives\SwitchDirective;
use DJWeb\Framework\View\Directives\UnlessDirective;
use DJWeb\Framework\View\Directives\WhileDirective;
use DJWeb\Framework\View\Directives\YieldDirective;
use DJWeb\Framework\View\TemplateCompiler;
use DJWeb\Framework\View\TemplateLoader;

class BladeAdapter extends BaseAdapter implements RendererContract
{
    private TemplateCompiler $compiler;
    private TemplateLoader $loader;
    private AssetManager $assetManager;

    private ?string $extendedTemplate = null;
    /**
     * @var array<string, string>
     */
    private array $sections = [];
    private string $currentSection = '';

    public function __construct(
        private string $template_path,
        private string $cache_path,
        public readonly string $componentNamespace = '\\App\\View\\Components',
    ) {
        $this->compiler = new TemplateCompiler();
        $this->loader = new TemplateLoader($template_path);
        $this->assetManager = new AssetManager();
        $this->registerDefaultDirectives();
    }

    public function extend(string $template): void
    {
        $this->extendedTemplate = $template;
    }

    public function section(string $name): void
    {
       $this->currentSection = $name;
        ob_start();
    }

    public function endSection(): void
    {

        if ($this->currentSection) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = '';
        }
    }

    public function yield(string $section): string
    {
        return $this->sections[$section] ?? '';
    }

    public function pushToStack(string $stack, string $content): void
    {
        $this->assetManager->push($stack, $content);
    }

    public function renderStack(string $stack): string
    {
        return $this->assetManager->render($stack);
    }

    public function render(string $template, array $data = []): string
    {
        $this->extendedTemplate = null;

        $content = $this->renderTemplate($template, $data);

        if ($this->extendedTemplate !== null) {
            return $this->render($this->extendedTemplate, $data);
        }

        return $content;
    }

    public static function buildDefault(): RendererContract
    {
        $config = Config::get('views.engines.blade');
        $template_path = $config['paths']['template_path'];
        $cache_path = $config['paths']['cache_path'];
        $namespace = $config['components']['namespace'];
        return new BladeAdapter($template_path, $cache_path, $namespace);
    }

    private function renderTemplate(string $template, array $data): string
    {
        $cached_file = $this->getCachedPath($template);

        if (! $this->isCached($template, $cached_file)) {
            $content = $this->loader->load($template);
            $compiled = $this->compiler->compile($content);
            $this->cache($cached_file, $compiled);
        }

        return $this->evaluateTemplate($cached_file, $data);
    }

    private function registerDefaultDirectives(): void
    {
        $this->compiler
            ->addDirective(new ExtendDirective())
            ->addDirective(new SectionDirective())
            ->addDirective(new YieldDirective())
            ->addDirective(new ComponentDirective())
            ->addDirective(new SlotDirective())
            ->addDirective(new StackDirective())
            ->addDirective(new IfDirective())
            ->addDirective(new UnlessDirective())
            ->addDirective(new ForDirective())
            ->addDirective(new ForeachDirective())
            ->addDirective(new WhileDirective())
            ->addDirective(new DoWhileDirective())
            ->addDirective(new SwitchDirective())
            ->addDirective(new IssetDirective())
            ->addDirective(new EmptyDirective())
            ->addDirective(new YieldDirective());
    }

    private function isCached(string $template, string $cached_file): bool
    {
        $template_path = $this->template_path . '/' . $template;
        return File::isCached($template_path, $cached_file);
    }

    private function getCachedPath(string $template): string
    {
        return $this->cache_path . '/' . md5($template) . '.php';
    }

    private function cache(string $path, string $content): void
    {
        File::create($path, $content);
    }

    private function evaluateTemplate(string $cached_file, array $data): string
    {
        extract($data);
        ob_start();
        include_once $cached_file;
        return ob_get_clean();
    }
}
