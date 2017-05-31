<?php namespace spec\Opbeat;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Exception;

class ClientSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'organization_id' => '...',
            'app_id' => '...',
            'access_token' => '...'
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Opbeat\Client');
    }

    function it_should_register_exception_handler()
    {
        $expectedHandler = [$this->getWrappedObject(), 'catchException'];
        $this->exceptionHandler()->shouldReturn(set_exception_handler(null));
    }

    function it_should_not_register_exception_handler_when_passed_false()
    {
        $this->beConstructedWith([
            'organization_id' => '...',
            'app_id' => '...',
            'access_token' => '...',
            'enable_exception_handler' => false
        ]);

        $expectedHandler = [$this->getWrappedObject(), 'catchException'];
        $this->exceptionHandler()->shouldNotReturn(set_exception_handler(null));
    }

    function it_should_set_error_handler()
    {
        $errorHandler = [$this->getWrappedObject(), ['handleError']];
        $this->errorHandler()->shouldReturn(set_error_handler(null));
    }

    function it_should_not_set_error_handler_when_passed_false()
    {
        $this->beConstructedWith([
            'organization_id' => '...',
            'app_id' => '...',
            'access_token' => '...',
            'enable_error_handler' => false
        ]);

        $errorHandler = [$this->getWrappedObject(), ['handleError']];
        $this->errorHandler()->shouldNotReturn(set_error_handler(null));
    }
}
