<?php
	/**
	*Обработка статей
	*/
	class Article
	{
		public $id = null;
		public $date = null;
		public $title  null;
		public $summary = null;
		public $content = null;
		
		public function __construct($data=array() )
		{
			if (isset($data['id'])) $this->id=(int) $data['id'];
			if (isset($data['date'])) $this->date=(int) $data['date'];
			if (isset($data['title'])) $this->title=preg_replace("/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['title']);
			if (isset($data['summary'])) $this->summary = preg_replace ("/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['summary']);
			if (isset($data['content'])) $this->content = $data['content'];
		}
		
		public function storeFormValues($params)
		{
			$this->__construct($params);
			if (isset($params['date']))
			{
				$date = explode('-', $params['date']);
				if(count($date) == 3) 
				{
					list($y, $m, $d) = $date;
					$this->date = mktime(0,0,0, $m,$d,$y);
				}
			}
		}
		
		public static function getById($id)
		{
			$conn = new PDO(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
			$query = "SELECT * UNIX_TIMESTAMP(date) AS date FROM articles WHERE id = :id";
			$st = $conn->prepare($query);
			$st->bindValue(":id", $id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			$conn=null;
			if($row) return new Article($row);
		}
		
		public static function getList ($numRows=100000, $order="date DESC")
		{
			$conn = new PDO(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
			$query = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(date) AS date FROM articles ORDER BY " .
			mysql_escape_string($order) . " LIMIT :numRows";
			
			$st = $conn->prepare($sql);
			$st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
			$st->execute();
			$list = array();
			
			while ($row = $st->fetch())
			{
				$article=new Article($row);
				$list[] = $article;
			}
			
			$query = "SELECT FOUND_ROWS() AS totalRows";
			$totalRows = $conn->query($query)->fetch();
			$conn = null;
			return (array("result" => $list, "totalRows"=>$totalRows[0]));
		}
		
		public function insert()
		{
			if (!is_null($this->id)) trigger_error("Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR );
			$conn = new PDO(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
			$query = "INSERT INTO articles (date, title, summary, content) VALUE (FROM_UNIXTIME(:date),:title, :summary, :content)";
			$st = $conn->prepare($query);
			$st->bindValue(":date", $this->date, PDO::PARAM_INT);
			$st->bindValue(":title", $this->title, PDO::PARAM_STR);
			$st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
			$st->bindValue(":content", $this->content, PDO::PARAM_STR);
			$st->execute();
			$this-id = $conn->lastInsertId();
			$conn = null;
		}
		
		public function update()
		{
			if ( is_null( $this->id ) ) trigger_error ( "Article::update(): Attempt to update an Article object that does not have its ID property set.", E_USER_ERROR );
			$conn = new PDO(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
			$query = "UPDATE articles SET date=FROM_UNIXTIME(:date), title=:title, summary=:summary, content=:content WHERE id=:id";
			$st = $conn->prepare($query);
			$st->bindValue(":date", $this->date, PDO::PARAM_INT);
			$st->bindValue(":title", $this->title, PDO::PARAM_STR);
			$st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
			$st->bindValue(":content", $this->content, PDO::PARAM_STR);
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->execute();
			$conn = null;
		}
		
		public function delete() 
		{
			if ( is_null( $this->id ) ) trigger_error ( "Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR );
			$conn = new PDO(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
			$st = $conn->prepare("DELETE FROM articles WHERE id = :id LIMIT 1");
			$st->bindValue(":id", $this->id, PDO::PARAM_INT);
			$st->execute();
			$conn = null;
		}
	}
?>
