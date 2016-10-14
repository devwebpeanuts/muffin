<?php

use Muffin\Queries;
use Muffin\Conditions;
use Muffin\Types;
use Muffin\Tests\Escapers\SimpleEscaper;
use Muffin\Queries\Snippets\OrderBy;
use Muffin\Tests\QueryParts\IsCute;

class DeleteTest extends PHPUnit_Framework_TestCase
{
    protected
        $escaper;

    protected function setUp()
    {
        $this->escaper = new SimpleEscaper();
    }

    public function testDeleteSingleTable()
    {
        $query = (new Queries\Delete())->setEscaper($this->escaper);

        $query
            ->from('burger', 'b')
            ->where(new Conditions\Equal(new Types\String('type'), 'healthy'))
        ;

        $this->assertSame("DELETE FROM burger AS b WHERE type = 'healthy'", $query->toString($this->escaper));

        $query->orderBy('date', OrderBy::DESC);

        $this->assertSame("DELETE FROM burger AS b WHERE type = 'healthy' ORDER BY date DESC", $query->toString($this->escaper));

        $query
            ->limit(12)
            ->offset(5)
        ;

        $this->assertSame("DELETE FROM burger AS b WHERE type = 'healthy' ORDER BY date DESC LIMIT 12 OFFSET 5", $query->toString($this->escaper));
    }

    public function testDeleteWithInnerJoin()
    {
        $query = (new Queries\Delete())->setEscaper($this->escaper);

        $query
            ->from('burger', 'b')
            ->where(new Conditions\Equal(new Types\String('type'), 'healthy'))
            ->innerJoin('taste', 't')->on('b.taste_id', 't.id')
        ;

        $this->assertSame("DELETE FROM burger AS b INNER JOIN taste AS t ON b.taste_id = t.id WHERE type = 'healthy'", $query->toString($this->escaper));
    }

    public function testDeleteWithLeftJoin()
    {
        $query = (new Queries\Delete())->setEscaper($this->escaper);

        $query
            ->from('burger', 'b')
            ->where(new Conditions\Equal(new Types\String('type'), 'healthy'))
            ->leftJoin('taste', 't')->on('b.taste_id', 't.id')
        ;

        $this->assertSame("DELETE FROM burger AS b LEFT JOIN taste AS t ON b.taste_id = t.id WHERE type = 'healthy'", $query->toString($this->escaper));
    }

    public function testDeleteWithRightJoin()
    {
        $query = (new Queries\Delete())->setEscaper($this->escaper);

        $query
            ->from('burger', 'b')
            ->where(new Conditions\Equal(new Types\String('type'), 'healthy'))
            ->rightJoin('taste', 't')->on('b.taste_id', 't.id')
        ;

        $this->assertSame("DELETE FROM burger AS b RIGHT JOIN taste AS t ON b.taste_id = t.id WHERE type = 'healthy'", $query->toString($this->escaper));
    }

    public function testDeleteUsingConstructor()
    {
        $query = (new Queries\Delete('burger', 'b'))->setEscaper($this->escaper);

        $query
            ->where(new Conditions\Equal(new Types\String('type'), 'healthy'))
        ;

        $this->assertSame("DELETE FROM burger AS b WHERE type = 'healthy'", $query->toString($this->escaper));
    }

    /**
     * @expectedException \LogicException
     */
    public function testDeleteWithoutFrom()
    {
        $query = (new Queries\Delete())->setEscaper($this->escaper);

        $query->toString();
    }

    public function testDeleteWithQueryPart()
    {
        $query = (new Queries\Delete('burger', 'b'))->setEscaper($this->escaper);

        $query
            ->where(new Conditions\Equal(new Types\String('owner'), 'Claude'))
            ->add(new IsCute());
        ;

        $this->assertSame("DELETE FROM burger AS b WHERE owner = 'Claude' AND color = 'white' AND age < 1", $query->toString($this->escaper));
    }
}
