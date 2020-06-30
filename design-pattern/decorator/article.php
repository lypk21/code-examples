<?php

class  BaseArticle {
    protected  $article = null;
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }
    public function decorate() {
        return $this->content;
    }
}

class EditorArticle extends  BaseArticle {
    public function __construct(BaseArticle $article)
    {
        $this->article = $article;
    }
    public function decorate() {
        return $this->article->decorate()." Editor add header and footer\n";
    }
}

class SEOArticle extends BaseArticle {
    public function __construct(BaseArticle $article)
    {
        $this->article = $article;
    }

    public function decorate()
    {
        return $this->article->decorate()."SEO add keywords and description.\n";
    }
}

$article = new SEOArticle(new BaseArticle("This is a base article\n"));
echo $article->decorate();

$article = new EditorArticle(new SEOArticle(new BaseArticle("This is a base article\n")));
echo $article->decorate();

$article = new SEOArticle(new EditorArticle(new BaseArticle("This is a base article\n")));
echo $article->decorate();
