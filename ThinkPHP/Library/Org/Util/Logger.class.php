<?php 
/**
 * 日志文件操作类
 */

namespace Org\Util;

class Logger {
	
	/** 严重错误记录 */
	function error($msg) {
		return $this->write($msg, 'error');
	}
	
	/** 调试信息记录 */
	function debug($msg) {
		return self::write($msg, 'debug');
	}

	/** 警告记录 */
	function warn($msg) {
		return self::write($msg, 'warn');
	}
	
	/** 写日志文件 */
	function write($msg, $type) {
		$msg = serialize($msg);
		$path = LOG_PATH.'other/'.date('Ym').'/';
		!is_dir($path) && mkdir($path, 0777, true); // 日志目录
		$logFile = $path.$type.'_'.date('d').'.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] {$msg} \n";
		error_log($msg, 3, $logFile);
	}
	
	/** die方法 */
	function halt($msg) {
		die($msg);
	}
	
	/** 数据库错误记录 */
	function dbwrite($funcName, $errMsg, $parms) {
		$parms = json_encode($parms);
		$errMsg = json_encode($errMsg);
		$path = LOG_PATH.'db/'.date('Ym').'/';
		!is_dir($path) && mkdir($path, 0777, true); // 日志目录
		$logFile = $path.date('Ymd').'.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] [{$funcName}] [{$errMsg}] [{$parms}] \n";
		error_log($msg, 3, $logFile);
	}

	
}
?>