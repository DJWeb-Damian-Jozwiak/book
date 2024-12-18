<?php

namespace Tests\Http;

use DJWeb\Framework\Http\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testToString()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $stream->rewind();
        $this->assertEquals('Hello, World!', (string)$stream);
    }

    public function testSetContent()
    {
        $stream = new Stream();
        $stream->withContent('Hello, World!');
        $this->assertEquals('Hello, World!', $stream->getContents());
    }

    public function testBrokenStream()
    {
        $object = new Stream('php://memory', 'w');

        $result = (string)$object;

        $this->assertEquals('', $result);
    }

    public function testClose()
    {
        $stream = new Stream();
        $stream->close();
        $this->assertFalse(is_resource($stream->detach()));
    }

    public function testCloseMethodDoesNothingIfStreamIsAlreadyClosed(): void
    {
        $stream = new Stream();
        $stream->close(); // Close once
        $streamBefore = $this->getStreamResourceFromBaseStream($stream);

        $stream->close(); // Close again
        $streamAfter = $this->getStreamResourceFromBaseStream($stream);

        $this->assertSame($streamBefore, $streamAfter);
    }


    private function getStreamResourceFromBaseStream($stream)
    {
        $reflection = new \ReflectionClass($stream);
        $streamProperty = $reflection->getProperty('stream');
        $streamProperty->setAccessible(true);
        return $streamProperty->getValue($stream);
    }

    public function testDetach()
    {
        $stream = new Stream();
        $resource = $stream->detach();
        $this->assertIsResource($resource);
        $this->assertNull($stream->detach());
    }

    public function testGetSize()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $this->assertEquals(13, $stream->getSize());
    }

    public function testTell()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $this->assertEquals(13, $stream->tell());
    }

    public function testEof()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $stream->read(13);
        $this->assertTrue($stream->eof());
    }

    public function testSeek()
    {
        $stream = new Stream();
        $stream->seek(0);
        $this->assertEquals('', (string)$stream);
    }

    public function testIsSeekable()
    {
        $stream = new Stream();
        $this->assertTrue($stream->isSeekable());
    }

    public function testIsWritable()
    {
        $stream = new Stream();
        $this->assertTrue($stream->isWritable());
    }

    public function testWrite()
    {
        $stream = new Stream();
        $this->assertEquals(13, $stream->write('Hello, World!'));
    }

    public function testIsReadable()
    {
        $stream = new Stream();
        $this->assertTrue($stream->isReadable());
    }

    public function testRead()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $stream->rewind();
        $this->assertEquals('Hello', $stream->read(5));
        $stream->close();
        unset($stream);
        gc_collect_cycles();
    }

    public function testGetContents()
    {
        $stream = new Stream();
        $stream->write('Hello, World!');
        $stream->rewind();
        $this->assertEquals('Hello, World!', $stream->getContents());
    }

    public function testGetMetadata()
    {
        $stream = new Stream();
        $meta = $stream->getMetadata();
        $this->assertInstanceOf(Stream\StreamMetaData::class, $meta);
    }

}