<?php

namespace spec\Http\Promise;

use Http\Promise\Promise;
use Psr\Http\Message\ResponseInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RejectedPromiseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \Exception());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Promise\RejectedPromise');
    }

    function it_is_a_promise()
    {
        $this->shouldImplement('Http\Promise\Promise');
    }

    function it_returns_a_fulfilled_promise(ResponseInterface $response)
    {
        $exception = new \Exception();
        $this->beConstructedWith($exception);

        $promise = $this->then(null, function (\Exception $exceptionReceived) use($exception, $response) {
            if (Argument::is($exceptionReceived)->scoreArgument($exception)) {
                return $response->getWrappedObject();
            }
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\FulfilledPromise');
        $promise->getState()->shouldReturn(Promise::FULFILLED);
        $promise->wait()->shouldReturn($response);
    }

    function it_returns_a_rejected_promise()
    {
        $exception = new \Exception();
        $this->beConstructedWith($exception);

        $promise = $this->then(null, function (\Exception $exceptionReceived) use($exception) {
            if (Argument::is($exceptionReceived)->scoreArgument($exception)) {
                throw $exception;
            }
        });

        $promise->shouldHaveType('Http\Promise\Promise');
        $promise->shouldHaveType('Http\Promise\RejectedPromise');
        $promise->getState()->shouldReturn(Promise::REJECTED);
        $promise->shouldThrow($exception)->duringWait();
    }

    function it_is_in_rejected_state()
    {
        $this->getState()->shouldReturn(Promise::REJECTED);
    }

    function it_returns_an_exception()
    {
        $exception = new \Exception();

        $this->beConstructedWith($exception);
        $this->shouldThrow($exception)->duringWait();
    }
}