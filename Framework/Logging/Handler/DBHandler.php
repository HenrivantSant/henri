<?php


namespace Henri\Framework\Logging\Handler;

use Exception;
use Henri\Framework\Logging\Entity\LogEntity;
use Henri\Framework\Logging\SystemLogger;
use Monolog\DateTimeImmutable;
use Monolog\Handler\AbstractProcessingHandler;

class DBHandler extends AbstractProcessingHandler {

    /**
     * @var LogEntity $entityLog
     */
    private $entityLog;

    /**
     * @var SystemLogger $systemLogger
     */
    private $systemLogger;

    /**
     * DBHandler constructor.
     *
     * @param LogEntity $entityLog
     * @param SystemLogger $systemLogger
     */
    public function __construct( LogEntity $entityLog, SystemLogger $systemLogger ) {
        $this->entityLog    = $entityLog;
        $this->systemLogger = $systemLogger;
    }

    /**
     * @param array $record
     */
    protected function write( array $record ): void {
        try {
            $this->entityLog->reset();

            $dataFormat            = $this->entityLog->getValuesAsObject();
            $dataFormat->channel   = $record['channel'];
            $dataFormat->message   = $record['message'];
            $dataFormat->context   = $record['context'];
            $dataFormat->level     = $record['level'];
            $dataFormat->levelName = $record['level_name'];
            /** @var DateTimeImmutable $date */
            $date                 = $record['datetime'];
            $dataFormat->datetime = $date->format( 'Y-m-d H:i:s' );
            $this->entityLog->populateState( $dataFormat, false );
            $this->entityLog->save();
            $this->entityLog->reset();
        } catch ( Exception $exception ) {
            $this->systemLogger->error('Cound not save log record to database in DBHandler', array(
                'record' => $record,
                'exception' => $exception->getMessage(),
            ));
        }
    }
}