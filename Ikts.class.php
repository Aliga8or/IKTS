<?php
class Ikts
{
	public static $exact = 30;
	public static $gd = 20;
	public static $correct = 10;
	public static $wrong = -10;
	public static $multiplier = 2;
	public static $risk = 1.5;
	public static $curr_round;
	public static $feature_risk = 1;
	
	public static function getGlobals()
	{
		$query = "select * from global";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		$row = mysql_fetch_assoc($result);
		
		Ikts::$curr_round = $row['curr_round'];
		Ikts::$feature_risk = $row['feature_risk'];
	}
	
	public static function setGlobals()
	{
		$query = "update global set curr_round = '".Ikts::$curr_round."',
									 feature_risk = '".Ikts::$feature_risk."'
									 where gid = 1
				 ";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function addFixture($teamA, $teamB, $round)
	{
		$query = "insert into fixture (teamA,
									   teamB,
									   round
									   )
									     values('".$teamA."',
												'".$teamB."',
												'".$round."'
												)
				";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		
		//populate the main table with new fixture - player combinations
		$fid = mysql_insert_id();
		$query = "select player from player";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $row = mysql_fetch_assoc($result) )
		{
			$query = "insert into main (player,
										fixture,
										round
										)
										 values('".$row['player']."',
												'".$fid."',
												'".$round."'
												)
					";
			mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		}
	}
	
	public static function deleteFixture($fid)
	{
		$query = "delete from fixture where fid=".$fid;
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		
		$query = "delete from main where fixture=".$fid;
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function updateFixture($fid, $scoreA, $scoreB )
	{
		$query = "update fixture set scoreA='".$scoreA."',
								   scoreB='".$scoreB."'
								   where fid='".$fid."'
				";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function getPrediction($mid, $predA, $predB, $banker)
	{
		if( ($predA - $predB) > 0 )
		{
			$zone = 'A';
		}
		elseif( ($predA - $predB) < 0 )
		{
			$zone = 'B';
		}
		else
		{
			$zone = 'AB';
		}
		
		$query = "update main set predA='".$predA."',
								   predB='".$predB."',
								   zone='".$zone."',
								   banker='".$banker."',
								   confirmed=1
								   where mid='".$mid."'
				";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function addPlayer($player)
	{
		$query = "insert into player (player
									  )
									    values('".$player."'
												)
				";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		
		//populate the main table with new player - fixture combinations
		$query = "delete from main where player='".$player."' ";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		
		$query = "select * from fixture";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $row = mysql_fetch_assoc($result) )
		{
			$query = "insert into main (player,
										fixture,
										round
										)
										 values('".$player."',
												'".$row['fid']."',
												'".$row['round']."'
												)
					";
			mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		}
	}
	
	public static function deletePlayer($player)
	{
		$query = "delete from player where player='".$player."' ";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		
		$query = "delete from main where player='".$player."' ";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function isAdmin($player)
	{
		$query = "select * from player where player='".$player."'";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		if( $row = mysql_fetch_assoc($result) )
		{
			return $row['admin'];
		}
		
		return 0;
	}
	
	public static function closeRound($round)
	{
		Ikts::evalRisk($round);
		$query = "update global set curr_round='".($round + 1)."' where gid=1 ";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function evalRisk($round)
	{
		$query = "select * from fixture where round=".$round;
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $fixture = mysql_fetch_assoc($result) )
		{
			$query1 = "select * from main where fixture=".$fixture['fid']." and confirmed=1 ";
			$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
			while( $main = mysql_fetch_assoc($result1) )
			{
				$query2 = "select mid from main where zone='".$main['zone']."' and
													  fixture='".$main['fixture']."' and
													  mid <> '".$main['mid']."' and
													  confirmed=1
							";
				$result2 = mysql_query($query2) or die ("Error in query: $query2. ".mysql_error());
				
				$risk = 0;
				if (mysql_num_rows($result2)==0)
				{
					$risk = 1;
				}
				
				$query3 = "update main set risk='".$risk."'
										   where mid='".$main['mid']."'
						";
				mysql_query($query3) or die ("Error in query: $query3. ".mysql_error());
			}
		}
	}
	
	public static function updateGame($round)
	{
		Ikts::evalRisk($round);
		Ikts::evalMain($round);
		Ikts::evalScore($round);
		Ikts::fillPlayer($round);
	}
	
	public static function evalMain($round)
	{
		$query = "select * from fixture where round=".$round;
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $fixture = mysql_fetch_assoc($result) )
		{
			$query1 = "select * from main where fixture=".$fixture['fid']." and confirmed=1 ";
			$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
			while( $main = mysql_fetch_assoc($result1) )
			{
				$p30 = ($main['predA'] == $fixture['scoreA']) && ($main['predB'] == $fixture['scoreB']);
				$p20 = ($main['predA'] - $main['predB']) == ($fixture['scoreA'] - $fixture['scoreB']);
				$p10 = ( ($main['predA'] - $main['predB']) < 0 && ($fixture['scoreA'] - $fixture['scoreB']) < 0 )
						|| ( ($main['predA'] - $main['predB']) > 0 && ($fixture['scoreA'] - $fixture['scoreB']) > 0 );
				
				if($p30)
				{
					$pt = Ikts::$exact;
				}
				elseif($p20)
				{
					$pt = Ikts::$gd;
				}
				elseif($p10)
				{
					$pt = Ikts::$correct;
				}
				else
				{
					$pt = Ikts::$wrong;
				}
				
				$ept = $pt;
				//banker processing
				if($main['banker'])
				{
					$ept = Ikts::$multiplier * $ept;
				}
				//risk processing
				if( Ikts::$feature_risk && $main['risk'] && ($ept > 0) )
				{
					$ept = Ikts::$risk * $ept;
				}
				
				$query2 = "update main set pts='".$pt."',
										   eff_pts='".$ept."'
										   where mid='".$main['mid']."'
						";
				mysql_query($query2) or die ("Error in query: $query2. ".mysql_error());
			}
		}
	}
	
	public static function evalScore($round)
	{
		$query = "select player from player";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $player = mysql_fetch_assoc($result) )
		{
			$pts = 0;
			$query1 = "select * from main where player='".$player['player']."' and round='".$round."' ";
			$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
			while( $main = mysql_fetch_assoc($result1) )
			{
				$pts += $main['eff_pts'];
			}
			
			$query2 = "select sid from score where player='".$player['player']."' and round='".$round."' ";
			$result2 = mysql_query($query2) or die ("Error in query: $query2. ".mysql_error());
			if (mysql_num_rows($result2)==0)
			{
				$query3 = "insert into score (player,
											 round,
											 pts
											)
											 values('".$player['player']."',
													 '".$round."',
													 '".$pts."'
													)
						";
				mysql_query($query3) or die ("Error in query: $query3. ".mysql_error());
			}
			else
			{
				$query3 = "update score set pts='".$pts."'
										   where player='".$player['player']."' and 
												 round='".$round."'
						";
				mysql_query($query3) or die ("Error in query: $query3. ".mysql_error());
			}
			
			Ikts::evalAggPts($player['player'], $round);
		}
		
		Ikts::evalRank($round);
		Ikts::evalAggRank($round);
	}
	
	public static function evalAggPts($player, $round)
	{
		$prevRd = $round - 1;
		$prevAgg = 0;
		$agg = 0;
		
		$query = "select * from score where player='".$player."' and round='".$prevRd."' ";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		if($row = mysql_fetch_assoc($result))
		{
			$prevAgg = $row['agg_pts'];
		}
		
		$query = "select * from score where player='".$player."' and round='".$round."' ";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		$row = mysql_fetch_assoc($result);
		$agg = $prevAgg + $row['pts'];
		
		$query = "update score set agg_pts='".$agg."'
								   where player='".$player."' and 
								   round='".$round."'
						";
		mysql_query($query) or die ("Error in query: $query. ".mysql_error());
	}
	
	public static function evalRank($round)
	{
		$rank = 0;
		$count = 0;
		$prev = -10000;
		
		$query = "select * from score where round='".$round."' order by pts desc";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $row = mysql_fetch_assoc($result) )
		{
			$count++;
			if( $row['pts'] != $prev )
			{
				$rank = $count;
			}
			$prev = $row['pts'];
			
			$query1 = "update score set rank='".$rank."'
									   where sid='".$row['sid']."'
						";
			mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
		}
	}
	
	public static function evalAggRank($round)
	{
		$rank = 0;
		$count = 0;
		$prev = -10000;
		
		$query = "select * from score where round='".$round."' order by agg_pts desc";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $row = mysql_fetch_assoc($result) )
		{
			$count++;
			if( $row['agg_pts'] != $prev )
			{
				$rank = $count;
			}
			$prev = $row['agg_pts'];
			
			$query1 = "update score set agg_rank='".$rank."'
									   where sid='".$row['sid']."'
						";
			mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
			
			Ikts::evalTrans($row['player'], $row['round'], $rank, $row['sid']);
		}
	}
	
	public static function evalTrans($player, $round, $aggRank, $sid)
	{
		$prevRd = $round - 1;
		$trans = 0;
		
		$query = "select * from score where player='".$player."' and round='".$prevRd."' ";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		if($prev = mysql_fetch_assoc($result))
		{
			$trans = $prev['agg_rank'] - $aggRank;
		}
		
		$query1 = "update score set trans='".$trans."'
									where sid='".$sid."'
					";
		mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
	}
	
	public static function fillPlayer($round)
	{
		$query = "select player from player";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		while( $player = mysql_fetch_assoc($result) )
		{
			$query1 = "select * from score where player='".$player['player']."' and round='".$round."' ";
			$result1 = mysql_query($query1) or die ("Error in query: $query1. ".mysql_error());
			$score = mysql_fetch_assoc($result1);
			
			$query2 = "update player set rank='".$score['agg_rank']."',
										 total='".$score['agg_pts']."'
										 where player='".$player['player']."'
						";
			mysql_query($query2) or die ("Error in query: $query2. ".mysql_error());
		}
	}
	
	public static function deadline($round)
	{
		date_default_timezone_set('UTC');
		$query = "select * from round where round='".$round."'";
		$result = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
		if( $rd = mysql_fetch_assoc($result) )
		{
			return strtotime($rd['deadline']);
		}
		return strtotime('15-7-2014 00:00:00');
	}
	
}
?>