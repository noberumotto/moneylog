<?php

namespace app\common\library\ems;



class Template
{
    protected $html = '';

    public function __construct()
    {
        //  加载html模板
        $tempFile = ROOT_PATH . 'application' . DS . 'common' . DS . 'library' . DS . 'ems' . DS . 'template.html';

        $this->html = file_get_contents($tempFile);

        $this->renderStyle();
    }

    public function setTitle($title)
    {
        $this->html = str_replace('{{title}}', $title, $this->html);

        return $this;
    }

    public function setContent($content)
    {
        $this->html = str_replace('{{content}}', $content, $this->html);
        return $this;
    }

    /**
     * 获得渲染后的内容
     */
    public function getBody()
    {
        return $this->html;
    }

    /**
     * 渲染style
     */
    private function renderStyle()
    {
        //  取出style部分
        preg_match("/<style>([\s\S]*)<\/style>/", $this->html, $styleMatch);

        $style = $styleMatch[1];

        //  读取样式
        preg_match_all("/\.(.*?)\{([\s\S]*?)\}/", $style, $styleArr);

        //  拼接样式

        preg_match_all("/\.(.*?)\{([\s\S]*?)\}/", $style, $styleArr);


        foreach ($styleArr[1] as $key => $item) {
            $item = preg_replace("/\s/", '', $item);

            $itemStyle = $styleArr[2][$key];

            $itemStyle = preg_replace("/[\n]/", '', $itemStyle);

            $this->html = str_replace("class=\"{$item}\"", "style=\"{$itemStyle}\"", $this->html);
        }
    }
}
