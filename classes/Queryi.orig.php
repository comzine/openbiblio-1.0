 * converted from mysql lib to mysqli by F.LaPlante Aug 2013
 * @author Micah Stetson
 */

require_once("../shared/common.php");

class Queryi extends mysqli
{
	private $lock_depth;

	public function __construct() {
		$this->lockDepth = 0;
        $this->dbConst = $dbConst;
        $this->setDSN();
        $this->dsn["mode"] == 'haveconst';

       if (($this->dsn["mode"] == 'nodb') || ($this->dsn["mode"] == 'noconst') || ($this->dsn["mode"] == '') ) {
     	    parent::__construct($this->dsn["host"], $this->dsn["username"], $this->dsn["pwd"]); // connect to db server - fl
        } else {
     	    parent::__construct($this->dsn["host"], $this->dsn["username"], $this->dsn["pwd"], $this->dsn["database"]); // connect to db server - fl
        }

        if (mysqli_connect_error()) {
            echo mysqli_connect_error()."<br>\n";
            return t("error: cannot connect to database server");
        } else {
            return "success, version# $mysqli->server_version";
        }
        	$this->set_encoding();
    }

    private function setDSN () {
        $fn = '../database_constants.php';
        if (file_exists($fn) ) {
            include($fn); // DO NOT change to 'include_once()' !!!!!
        } else {
            $this->dsn['host'] = 'localhost';
            $this->dsn['username'] = 'admin';
            $this->dsn['pwd'] = 'admin';
            $this->dsn['database'] = 'xxxopenbiblioxxx';
            $this->dsn['mode'] = 'nodb';
        }
    }
	public function act($sql) {
		//$this->lock();
		$results = $this->_act($sql);
		//$this->unlock();
		return $results;
	}
	public function select($sql) {
		$results = $this->_act($sql);
		if (!isset($results)) {
			return T("NothingFoundError");
		}
		return $results;
	}
	public function select1($sql) {
		$r = $this->select($sql);
		if ($r->num_rows != 1) {
		  return T("NothingFoundError");
		  //echo "sql=$sql<br />\n";
		} else {
			return $r->fetch_assoc();
		}
	}
	public function select01($sql) {
		$r = $this::act($sql);
		if (($r == 0) || ($r->num_rows == 0)) {
			return NULL;
		} else if ($r->num_rows != 1) {
			return T("Wrong Number Rows Found");
		} else {
			return $r->fetch_assoc();
		}
	}

	/** Make sure all queries have the user's preferred encoding
	 * and default to UTF8 if they have not chosen a valid encoding
	 *
	 * this may only work if complete OB database is in place; -FL
	*/
	private function set_encoding() {
		$r = parent::query("SELECT value FROM settings where name='charset'");
		if ($r->num_rows == 1) {
			$row = $r->fetch_assoc();
			if (!parent::set_charset($row['value']))  {
				parent::set_charset('utf8');
			}
		} else {
			parent::set_charset('utf8');
        }
	}
	private function _act($sql) {
		$r =  parent::query($sql);
		if ($r === false) {
			return 'Error: '.T("Database query failed");
		}
		return $r;
	}

	/* This is not easily portable to many SQL DBMSs.  A better scheme
	 * might be something like PEAR::DB's sequences.
	 */
	protected function getInsertID() {
		return $this->insert_id;
	}

	/** Locking functions -MS
	 *
	 * Besides switching to InnoDB for transactions, I haven't been able to
	 * come up with a good way to do locking reliably.  For now, we'll get
	 * and release one big advisory lock around every sensitive transaction
	 * and every database write except per-session data.  This should make
	 * everything work, even if it is heavy-handed.
	 *
	 * Calls to lock/unlock may be nested, but must be paired.
	 *
	 * ---- Locking temporarily disabled - not working with PHP5 msqli interface ----
	 */

	public function clearLocks () {
		$this->_act('set global read_only = off');
		$this->_act('unlock tables');
	}

	public function lock() {
		if ($this->lockDepth < 0) {
			Fatal::internalError(T("Negative lock depth"));
		}
/*
		if ($this->lockDepth == 0) {
			$row = $this->select1($this->mkSQL('select get_lock(%Q, %N) as locked',
				OBIB_LOCK_NAME, OBIB_LOCK_TIMEOUT));
			if (!isset($row['locked']) or $row['locked'] != 1) {
				Fatal::cantLock($row['locked']);
			}
		}
*/
		$this->lockDepth++;
	}
	public function unlock() {
		if ($this->lockDepth <= 0) {
			Fatal::internalError(T("Tried to unlock an unlocked database."));
		}
		$this->lockDepth--;
/*
		if ($this->lockDepth == 0) {
			$row = $this->select1($this->mkSQL('select release_lock(%Q) as unlocked',
				OBIB_LOCK_NAME));
			if (!isset($row['unlocked']) or $row['unlocked'] != 1) {
				Fatal::internalError(T("Cannot release lock"));
			}
		}
*/
	}

	/****************************************************************************
	 * Makes SQL by interpolating values into a format string. -MS
	 * This function works something like printf() for SQL queries.  Format
	 * strings contain %-escapes signifying that a value from the argument
	 * list should be inserted into the string at that point.  The routine
	 * properly quotes or transforms the value to make sure that it will be
	 * handled correctly by the SQL server.  The recognized format strings
	 * are as follows:
	 *  %% - is replaced by a single '%' character and does not consume a
	 *       value form the argument list.
	 *  %! - inserts the argument in the query unaltered -- BE CAREFUL!
	 *  %B - treates the argument as a boolean value and inserts either
	 *       'Y' or 'N'as appropriate.
	 *  %C - treats the argument as a column reference.  This differs from
	 *       %I below only in that it passes the '.' operator for separating
	 *       table and column names on to the SQL server unquoted.
	 *  %I - treats the argument as an identifier to be quoted.
	 *  %i - does the same escaping as %I, but does not add surrounding
	 *       quotation marks.
	 *  %N - treats the argument as a number and strips off all of it but
	 *       an initial numeric string with optional sign and decimal point.
	 *  %Q - treats the argument as a string and quotes it.
	 *  %q - does the same escaping as %Q, but does not add surrounding
	 *       quotation marks.
	 * @param string $fmt format string
	 * @param string ... optional argument values
	 * @return string the result of interpreting $fmt
	 * @access public
	 ****************************************************************************
	 */
	public function mkSQL() {
		$badSqlFmt = T("Bad mkSQL() format string.");

		$n = func_num_args();
		if ($n < 1) {
			Fatal::internalError(T("Not enough arguments given to mkSQL()."));
		}
		$i = 1;
		$SQL = "";
		$fmt = func_get_arg(0);
		while (strlen($fmt)) {
			$p = strpos($fmt, "%");
			if ($p === false) {
				$SQL .= $fmt;
				break;
			}
			$SQL .= substr($fmt, 0, $p);
			if (strlen($fmt) < $p+2) {
				Fatal::internalError($badSqlFmt);
			}
			if ($fmt{$p+1} == '%') {
				$SQL .= "%";
			} else {
				if ($i >= $n) {
					Fatal::internalError(T("Not enough arguments given to mkSQL()."));
				}
				$arg = func_get_arg($i++);
				switch ($fmt{$p+1}) {
				case '!':
					/* very dangerous, but sometimes very useful -- be careful */
					$SQL .= $arg;
					break;
				case 'B':
					if ($arg) {
						$SQL .= "'Y'";
					} else {
						$SQL .= "'N'";
					}
					break;
				case 'C':
					$a = array();
					foreach (explode('.', $arg) as $ident) {
						array_push($a, '`'.$this->_ident($ident).'`');
					}
					$SQL .= implode('.', $a);
					break;
				case 'I':
					$SQL .= '`'.$this->_ident($arg).'`';
					break;
				case 'i':
					$SQL .= $this->_ident($arg);
					break;
				case 'N':
					$SQL .= $this->_numstr($arg);
					break;
				case 'Q':
					$SQL .= "'".mysqli::real_escape_string($arg)."'";
					break;
				case 'q':
					$SQL .= mysqli::real_escape_string($arg);
					break;
				default:
					Fatal::internalError($badSqlFmt);
				}
			}
			$fmt = substr($fmt, $p+2);
		}
		if ($i != $n) {
			Fatal::internalError(T("Too many arguments to mkSQL()."));
		}
		return $SQL;
	}

	private function _ident($i) {
		# Because the MySQL manual is unclear on how to include a ` in a `-quoted
		# identifer, we just drop them.  The manual does not say whether backslash
		# escapes are interpreted in quoted identifiers, so I assume they are not. MS
		return str_replace('`', '', $i);
	}
	private function _numstr($n) {
		if (preg_match("/^([+-]?[0-9]+(\.[0-9]*)?([Ee][0-9]+)?)/", (string)$n, $subs)) {
			return $subs[1];
		} else {
			return "0";
		}
	}
}

/*
	## this class disabled - does not seem necessary with mysqli interface - FL
class DbIter extends Iter {
	function DbIter($results) {
		$this->results = $results;
	}
	function count() {
		return mysql_num_rows($this->results);
	}
	function next() {
		$r = mysql_fetch_assoc($this->results);
		if ($r === false) {
			return NULL;
		}
		return $r;
	}
}
*/
