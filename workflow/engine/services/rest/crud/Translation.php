<?php

class Services_Rest_Translation
{
    /**
     * Implementation for 'GET' method for Rest API
     *
     * @param  mixed $trnCategory, $trnId, $trnLang Primary key
     *
     * @return array $result Returns array within multiple records or a single record depending if
     *                       a single selection was requested passing id(s) as param
     */
    protected function get($trnCategory=null, $trnId=null, $trnLang=null)
    {
        $result = array();
        try {
            $noArguments = true;
            $argumentList = func_get_args();
            foreach ($argumentList as $arg) {
                if (!is_null($arg)) {
                    $noArguments = false;
                }
            }

            if ($noArguments) {
                $criteria = new Criteria('workflow');

                $criteria->addSelectColumn(TranslationPeer::TRN_CATEGORY);
                $criteria->addSelectColumn(TranslationPeer::TRN_ID);
                $criteria->addSelectColumn(TranslationPeer::TRN_LANG);
                $criteria->addSelectColumn(TranslationPeer::TRN_VALUE);
                $criteria->addSelectColumn(TranslationPeer::TRN_UPDATE_DATE);
                
                $dataset = AppEventPeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                while ($dataset->next()) {
                    $result[] = $dataset->getRow();
                }
            } else {
                $record = TranslationPeer::retrieveByPK($trnCategory, $trnId, $trnLang);
                if ($record) {
                    $result = $record->toArray(BasePeer::TYPE_FIELDNAME);
                } else {
                    $paramValues = "";
                    foreach ($argumentList as $arg) {
                        $paramValues .= (strlen($paramValues) ) ? ', ' : '';
                        if (!is_null($arg)) {
                            $paramValues .= "$arg";
                        } else {
                            $paramValues .= "NULL";
                        }
                    }
                    throw new RestException(417, "table Translation ($paramValues)" );
                }
            }
        } catch (RestException $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new RestException(412, $e->getMessage());
        }

        return $result;
    }


}
