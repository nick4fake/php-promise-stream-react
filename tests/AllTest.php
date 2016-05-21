<?php

use React\Stream\ReadableStream;
use Clue\React\Promise\Stream;
use React\Promise\CancellablePromiseInterface;
use React\Stream\WritableStream;

class AllTest extends TestCase
{
    public function testClosedStreamResolvesWithEmptyBuffer()
    {
        $stream = new ReadableStream();
        $stream->close();

        $promise = Stream\all($stream);

        $this->expectPromiseResolveWith(array(), $promise);
    }

    public function testClosedWritableStreamResolvesWithEmptyBuffer()
    {
        $stream = new WritableStream();
        $stream->close();

        $promise = Stream\all($stream);

        $this->expectPromiseResolveWith(array(), $promise);
    }

    public function testPendingStreamWillNotResolve()
    {
        $stream = new ReadableStream();

        $promise = Stream\all($stream);

        $promise->then($this->expectCallableNever(), $this->expectCallableNever());
    }

    public function testClosingStreamResolvesWithEmptyBuffer()
    {
        $stream = new ReadableStream();
        $promise = Stream\all($stream);

        $stream->close();

        $this->expectPromiseResolveWith(array(), $promise);
    }

    public function testClosingWritableStreamResolvesWithEmptyBuffer()
    {
        $stream = new WritableStream();
        $promise = Stream\all($stream);

        $stream->close();

        $this->expectPromiseResolveWith(array(), $promise);
    }

    public function testEmittingDataOnStreamResolvesWithArrayOfData()
    {
        $stream = new ReadableStream();
        $promise = Stream\all($stream);

        $stream->emit('data', array('hello', $stream));
        $stream->emit('data', array('world', $stream));
        $stream->close();

        $this->expectPromiseResolveWith(array('hello', 'world'), $promise);
    }

    public function testEmittingErrorOnStreamRejects()
    {
        $stream = new ReadableStream();
        $promise = Stream\all($stream);

        $stream->emit('error', array(new \RuntimeException('test')));

        $this->expectPromiseReject($promise);
    }

    public function testEmittingErrorAfterEmittingDataOnStreamRejects()
    {
        $stream = new ReadableStream();
        $promise = Stream\all($stream);

        $stream->emit('data', array('hello', $stream));
        $stream->emit('error', array(new \RuntimeException('test')));

        $this->expectPromiseReject($promise);
    }

    public function testCancelPendingStreamWillReject()
    {
        $stream = new ReadableStream();

        $promise = Stream\all($stream);

        $promise->cancel();

        $this->expectPromiseReject($promise);
    }
}
