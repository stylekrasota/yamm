<?phpnamespace bobroid\yamm;use yii\base\InvalidConfigException;use yii\base\Widget;use yii\helpers\Html;use yii\helpers\ArrayHelper;/** * @author Nikolai Gilko * @since 1.0 */class Yamm extends Widget{    /**     * @var array|string логотип, отображаемый в левой части меню     * если array:     *          content - логотип (ссылка на изображение), или текст     *          link    - ссылка на логотипе (по умолчанию '#')     */    public $logo = [];    public $items = [];    public $options = [];    public $theme;    /**     * @var array the dropdown widget options     */    public $dropdownOptions = [];        /**     * @var string the caret indicator to display for dropdowns     */    public $dropdownIndicator = ' <span class="caret"></span>';    protected $defaultMenuLabel = 'Меню';    protected $defaultSearchLabel = 'Поиск';        /**     * @inheritdoc     */    public function init()    {        $this->options['headerOptions'] = isset($this->options['headerOptions']) ? $this->options['headerOptions'] : [];        $this->options['headerOptions']['class'] = isset($this->options['headerOptions']['class']) ? 'cd-main-header ' . $this->options['headerOptions']['class'] : 'cd-main-header';        $this->options['menuLabel'] = isset($this->options['menuLabel']) ? $this->options['menuLabel'] : $this->defaultMenuLabel;        $this->options['searchLabel'] = isset($this->options['searchLabel']) ? $this->options['searchLabel'] : $this->defaultSearchLabel;        $a = new \bobroid\yamm\YammAsset();        if(isset($this->theme) && !empty($this->theme)){            $a->css[] = 'themes/'.$this->theme.'.css';        }        $a->register($this->getView());    }    public static function begin($config = []){        $tthis = parent::begin($config);        echo Html::tag('header', $tthis->renderLogo().Html::tag('ul', Html::tag('li', Html::tag('a', $tthis->options['searchLabel'].Html::tag('span'), [                    'class' =>  'cd-search-trigger',                    'href'  =>  '#cd-search'                ])).Html::tag('li', Html::tag('a', $tthis->options['menuLabel'].Html::tag('span'), [                    'class' =>  'cd-nav-trigger',                    'href'  =>  '#cd-primary-nav'                ])), [                'class' =>  'cd-header-buttons'            ]), $tthis->options['headerOptions']);        ;    }    public static function end(){        echo parent::end();    }    /**     * @inheritdoc     */    protected function isChildActive($items, &$active)    {    }    protected function renderLogo(){        $content = $link = '';        if(is_array($this->logo) && !empty($this->logo)){            $content = $this->logo['content'];            $link = $this->logo['link'];        }else{            if(empty($this->logo)){                return;            }            $content = $this->logo;        }        $link = empty($link) ? '#' : $link;        if(preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $content)){            $content = Html::tag('img', '', [                'src'   =>  $content            ]);        }        return Html::tag('a', $content, [            'class' =>  'cd-logo',            'href'  =>  $link        ]);    }    /**     * @inheritdoc     */    public function renderItem($item, $parent = [])    {        $dropdown = false;        $options = [];        if(isset($item['items']) && !empty($item['items'])){            $dropdown = true;            $options = [                'class' =>  'has-children'            ];        }        $r = Html::a(!isset($item['label']) ? $item : $item['label'], isset($item['url']) ? $item['url'] : '#');        if($dropdown){            $subitems = '';            if(empty($parent)){                if(!isset($item['options'])){                    $item['options']['class'] = 'cd-secondary-nav is-hidden';                }else{                    $item['options']['class'] .= isset($item['options']['type']) ? $item['options']['type'].' is-hidden' : 'cd-secondary-nav is-hidden';                }            }else{                if(!isset($item['options'])){                    $item['options']['class'] = 'is-hidden';                }else{                    $item['options']['class'] .= isset($item['options']['class']) ? $item['options']['class'].' is-hidden' : 'is-hidden';                }            }            $subitems .= Html::tag('li', Html::tag('a', isset($parent['label']) ? $parent['label'] : '', [                'href'  =>  '#0'            ]), [                'class' =>  'go-back'            ]);            foreach($item['items'] as $sItem){                $subitems .= $this->renderItem($sItem, $item);            }            $r .= Html::tag('ul', $subitems, $item['options']);        }        $r = Html::tag('li', $r, $options);        return $r;    }    protected function renderMenu(){        $items = '';        foreach($this->items as $item){            $items .= $this->renderItem($item);        }        $overlay = Html::tag('div', '', [            'class' =>  'cd-overlay'        ]);        $nav = Html::tag('nav', Html::tag('ul', $items, [            'class' =>  'cd-primary-nav is-fixed',            'id'    =>  'cd-primary-nav'        ]), [            'class' =>  'cd-nav'        ]);        $search = Html::tag('div', Html::tag('form', Html::tag('input', '', [            'type'          =>  'search',            'placeholder'   =>  isset($this->options['searchPlaceholder']) ? $this->options['searchPlaceholder'] : 'Поиск...'        ])), [            'class' =>  'cd-search',            'id'    =>  'cd-search'        ]);        return $overlay.$nav.$search;    }    public function run(){        return self::renderMenu();    }}