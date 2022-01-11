<?php

namespace Pantheon\Terminus\Tests\Functional;

/**
 * Class SolrCommandsTest.
 *
 * @package Pantheon\Terminus\Tests\Functional
 */
class SolrCommandsTest extends TerminusTestBase
{
    /**
     * @test
     * @covers \Pantheon\Terminus\Commands\Solr\EnableCommand
     *
     * @group solr
     * @group long
     */
    public function testSolrEnableCommand()
    {
        $this->terminus(sprintf('solr:enable %s', $this->getSiteName()));
    }

    /**
     * @test
     * @covers \Pantheon\Terminus\Commands\Solr\DisableCommand
     *
     * @group solr
     * @group long
     */
    public function testSolrDisableCommand()
    {
        $this->terminus(sprintf('solr:disable %s', $this->getSiteName()));
    }
}
