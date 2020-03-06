<?php
/**
 * @package   fisharebest/laravel-floats
 * @copyright 2020 "Greg Roach" <greg@subaqua.co.uk>
 * @licence   MIT
 */
namespace Fisharebest\LaravelFloats;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestLaravelFloats
 *
 * @covers \Fisharebest\LaravelFloats\Blueprint
 * @covers \Fisharebest\LaravelFloats\MySqlGrammar
 * @covers \Fisharebest\LaravelFloats\Schema
 */
class LaravelFloatsTest extends BaseTestCase
{
    /**
     * Test the Schema Facade.
     *
     * @return void
     */
    public function testMySqlSchemaWithNamedConnetion()
    {
        $mock_connection = $this->createMock(MySqlConnection::class);

        $mock_connection
            ->method('withTablePrefix')
            ->willReturn(new MySqlGrammar());

        $mock_connection
            ->method('setSchemaGrammar');

        $mock_connection
            ->method('getSchemaGrammar')
            ->willReturn(new MySqlGrammar());

        $mock_connection
            ->method('getSchemaBuilder')
            ->willReturn(new MySqlBuilder($mock_connection));

        $mock_database_manager = $this->createMock(DatabaseManager::class);

        $mock_database_manager
            ->method('connection')
            ->with('mysql')
            ->willReturn($mock_connection);

        $mock_application = [
            'db' => $mock_database_manager
        ];

        Facade::setFacadeApplication($mock_application);

        $schema_builder = Schema::connection('mysql');

        $this->assertInstanceOf(Builder::class, $schema_builder);

        $schema_builder->table('foo', function (Blueprint $blueprint) {
            // Generates our Blueprint, not the default one.
            $this->assertInstanceOf(Blueprint::class, $blueprint);
        });
    }

    /**
     * Test the Schema Facade.
     *
     * @return void
     */
    public function testMySqlSchemaWithDefaultConnection()
    {
        $mock_connection = $this->createMock(MySqlConnection::class);

        $mock_connection
            ->method('withTablePrefix')
            ->willReturn(new MySqlGrammar());

        $mock_connection
            ->method('setSchemaGrammar');

        $mock_connection
            ->method('getSchemaGrammar')
            ->willReturn(new MySqlGrammar());

        $mock_connection
            ->method('getSchemaBuilder')
            ->willReturn(new MySqlBuilder($mock_connection));

        $mock_database_manager = $this->createMock(DatabaseManager::class);

        $mock_database_manager
            ->method('connection')
            ->with(null)
            ->willReturn($mock_connection);

        $mock_database_manager
            ->method('connection')
            ->with(null)
            ->willReturn($mock_connection);

        $mock_application = [
            'db' => $mock_database_manager
        ];

        Facade::setFacadeApplication($mock_application);

        $schema_builder = Schema::getFacadeRoot();

        $this->assertInstanceOf(Builder::class, $schema_builder);

        $schema_builder->table('foo', function (Blueprint $blueprint) {
            // Generates our Blueprint, not the default one.
            $this->assertInstanceOf(Blueprint::class, $blueprint);
        });
    }

    /**
     * Test the Schema Facade.
     *
     * @return void
     */
    public function testNonMySqlSchema()
    {
        $mock_connection = $this->createMock(SqliteConnection::class);

        $mock_connection
            ->method('withTablePrefix')
            ->willReturn(new SQLiteGrammar());

        $mock_connection
            ->method('setSchemaGrammar');

        $mock_connection
            ->method('getSchemaGrammar')
            ->willReturn(new SQLiteGrammar());

        $mock_connection
            ->method('getSchemaBuilder')
            ->willReturn(new MySqlBuilder($mock_connection));

        $mock_database_manager = $this->createMock(DatabaseManager::class);

        $mock_database_manager
            ->method('connection')
            ->with('sqlite')
            ->willReturn($mock_connection);

        $mock_application = [
            'db' => $mock_database_manager
        ];

        Facade::setFacadeApplication($mock_application);

        $schema_builder = Schema::connection('sqlite');

        $this->assertInstanceOf(Builder::class, $schema_builder);
    }

    /**
     * Create a FLOAT with no scale or precision.
     *
     * @return void
     */
    public function testCreateFloatWithNoScaleOrPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c');

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with default scale or precision.
     *
     * @return void
     */
    public function testCreateFloatColumnWithDefaultScaleAndPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', null, null);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with both scale and precision.
     *
     * @return void
     */
    public function testCreateFloatColumnWithScaleAndPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', 5, 3);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float(5, 3) not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with zero scale and precision.
     *
     * @return void
     */
    public function testCreateFloatColumnWithZeroScaleAndPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', 0, 0);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with just scale. (Not valid)
     *
     * @return void
     */
    public function testCreateFloatColumnWithJustScale()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', 5);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with zero scale. (Not valid)
     *
     * @return void
     */
    public function testCreateFloatColumnWithZeroScale()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', 0);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with just precision. (Not valid)
     *
     * @return void
     */
    public function testCreateFloatColumnWithJustPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', null, 3);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Create a FLOAT with zero precision. (Not valid)
     *
     * @return void
     */
    public function testCreateFloatColumnWithZeroPrecision()
    {
        $connection = $this->createMock(Connection::class);

        $blueprint = new Blueprint('t');
        $blueprint->create();
        $blueprint->float('c', null, 0);

        $statements = $blueprint->toSql($connection, new MySqlGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('create table `t` (`c` float not null)', $statements[0]);
    }

    /**
     * Creating a FLOATs should work the same as creating a DOUBLE.
     *
     * @return void
     */
    public function testFloatAgainstDouble()
    {
        $connection = $this->createMock(Connection::class);

        foreach ([null, 0, 5] as $scale) {
            foreach ([null, 0, 3] as $precision) {
                $blueprint = new Blueprint('t');
                $blueprint->create();
                $blueprint->double('c', $scale, $precision);

                $statements = $blueprint->toSql($connection, new MySqlGrammar());

                $double_sql = $statements[0];

                $blueprint = new Blueprint('t');
                $blueprint->create();
                $blueprint->float('c', $scale, $precision);

                $statements = $blueprint->toSql($connection, new MySqlGrammar());

                $float_sql = $statements[0];

                $this->assertSame(str_replace('double', 'float', $double_sql), $float_sql);
            }
        }

    }
}
