<?php

namespace Dukrl\Bridge\Example;

abstract class RendererDefault {
    protected $renderer;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    public function changeNewRenderer(Renderer $renderer) : void {
        $this->renderer = $renderer;
    }

    abstract public function view() : string;
}

class Email {

    private $user, $subject, $body, $date;

    public function __construct(string $subject, string $body, string $user, string $date) {
        $this->subject = $subject;
        $this->body = $body;
        $this->user = $user;
        $this->date = $date;
    }

    public function getUser() : string {
        return $this->user;
    }

    public function getSubject() : string {
        return $this->subject;
    }

    public function getBody() : string {
        return $this->body;
    }

    public function getDate() : string {
        return $this->date;
    }
}

class LayoutDefault extends RendererDefault {
    protected $email;

    public function __construct(Renderer $renderer, Email $email) {
        parent::__construct($renderer);
        $this->email = $email;
    }

    public function view(): string {
        return $this->renderer->renderParts([
            $this->renderer->renderTitle($this->email->getUser(), $this->email->getSubject()),
            $this->renderer->renderBody($this->email->getBody()),
            $this->renderer->renderFooter($this->email->getDate(), $this->email->getUser())
        ]);
    }
}

interface Renderer {

    public function renderTitle(string $user, string $subject) : string;

    public function renderBody(string $body) : string;

    public function renderFooter(string $date, string $user) : string;

    public function renderParts(array $parts) : string;

}

class RenderJSON implements Renderer {

    public function renderTitle(string $user, string $subject) : string {
        return '    "title": "' . $user . ' - ' . $subject . '"';
    }

    public function renderBody(string $body) : string {
        return '    "body": "' . $body . '"';
    }

    public function renderFooter(string $date, string $user) : string {
        return '    "footer": "' . $user . ' - ' . $date . '"';
    }

    public function renderParts(array $parts): string
    {
        return "{\n" . implode(",\n", array_filter($parts)) . "\n}";
    }
}

class RendererStringOnly implements Renderer {

    public function renderTitle(string $user, string $subject) : string {
        return $user . ' - ' . $subject;
    }

    public function renderBody(string $body) : string {
        return $body;
    }

    public function renderFooter(string $date, string $user) : string {
        return $user . ' - ' . $date;
    }

    public function renderParts(array $parts): string
    {
        return implode("\n", $parts);
    }
}

function FrontCode(RendererDefault $default) {

    echo $default->view();

}

$rendererString = new RendererStringOnly();
$rendererJSON = new RenderJSON();

$mail = new Email('Título', 'corpo do email', 'Eduardo', 'Hoje');

$render = new LayoutDefault($rendererString, $mail);
echo "\nEsse trecho é referente ao render de uma string\n";
FrontCode($render);
echo "\n\n";

echo "---------------------------------------------------------------------";

$render->changeNewRenderer($rendererJSON);
echo "\nEsse trecho é referente ao render de um JSON\n";
FrontCode($render);
echo "\n\n";
