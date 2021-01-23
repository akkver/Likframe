<?php


namespace core;


class Response
{
    protected array $headers = [];
    protected string $content = '';
    protected int $code = 200;

    public function sendContent()
    {
        echo $this->content;
    }

    public function sendHeaders()
    {
        foreach ($this->headers as $key => $header) {
            header($key.': '.$header);
        }
    }

    /**
     * @return $this
     */
    public function send(): Response
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this;
    }

    public function setContent($content)
    {
        if (is_array($content)){
            $content = json_encode($content);
        }
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code)
    {
        $this->code = $code;
        return $this;
    }

}