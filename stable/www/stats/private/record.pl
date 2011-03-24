#!/usr/bin/perl -w

#print "Content-type: text/plain\n\n";

my ($page, $countKingdom);
my $thetime = time()+32400;
#	($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst)
my @tid = localtime("$thetime"); 
#	print "$tid[2] - $tid[1] - $tid[3] - $tid[4] - $tid[5] - $tid[6] - $tid[7] - $tid[8] - $tid[9]";
use DBI;
use strict;
use HTTP::Request::Common;
use LWP::UserAgent;
my $ua = LWP::UserAgent->new;

$ua->agent('Mozilla/5.0'); 
my $failed = 1;
my $i = 1;
my ($kingid,$prace,$content,$pnum,$pname,$pscore,$pacres,$pking,$datatemp,$dill,$line,$tick,$sistetid) = 0;
my (%kcount,@kacres,@kscore,@page);

my $req = HTTP::Request->new(GET => 'http://localhost/~tkocnet/scripts/statgenerator.php');
my $res = $ua->request($req);
 
if ($res->is_success) {
	  $content= $res->content;
} 
else {
	$failed=0;
}
	

if ($failed) {
	@page = split("\n", $content);
	foreach $line (@page) {
		if ($i=='1' || $i=='4') {
		}
		elsif ($i=='2') {
			($dill,$dill,$tick,$dill,$dill,$dill,$dill,$sistetid,$dill) = split(" ", $line,9);
			&oppdater_tid($sistetid);
		}
		elsif ($i=='3') {
		}
		else {	
			chomp $line;
			($pnum,$datatemp) = split(" ", $line,2);
			($dill,$pname,$datatemp) = split("\"", $datatemp,3);
			($prace,$datatemp) = split(" ", $datatemp,2);
			($pscore,$datatemp) = split(" ", $datatemp,2);
			if (!$pscore) {
				$pscore=0;
			}
			($pacres,$pking) = split(" ", $datatemp,2);
			if (!$pacres) {
				$pacres=0;
			}
			if (!$pking) {
				$pking=0;
			}
			if ($pking == '0') {
				$pking = $pacres;
				$pacres = $pscore;
				$pscore = 0;
			}			
#			($pking,$datatemp) = split(" ", $datatemp,2);
			$pname =~ s/'/&#39;/g;
			$pname =~ s/\\/&#92;/g;
#			print "-$pnum-$pname-$prace-$pscore-$pacres-$pking- \n";
			if ($pacres eq 'Deleted' || !$pacres) {
				$pacres = 0;
			}
			if ($pscore eq 'Deleted' || !$pscore) {
				$pscore = 0;
			}
			&insert_province_into_mysql($pname, $prace, $pscore, $pacres, $pking, $tick);
			if ($kcount{$pking}) {
				$kcount{$pking}++;
			}
			else {
				$kcount{$pking} = 1;
			}
			if ($kscore[$pking]) {
				$kscore[$pking] = $kscore[$pking] + $pscore;
			}
			else {
				$kscore[$pking] = $pscore;
			}
			if ($kacres[$pking]) {
				$kacres[$pking] = $kacres[$pking] + $pacres;	
			}
			else {
				$kacres[$pking] = $pacres;
			}		
		}
		$i++;
	}
	while (my ($key,$value) = each(%kcount)) {
		&insert_kingdom_into_mysql($key, $value, $kscore[$key], $kacres[$key], $tick);
	}
	&update_kd_placement();
	&update_prov_placement();
}


sub insert_kingstat_into_mysql {
	my ($kingid, $kingscore, $kingacres, $tick) = @_;
	
	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";

	my $query = $dbh->do("SELECT * FROM kingdomstat WHERE kingdomId='$kingid' AND time='$tick'") || die "Insert failed: $DBI::errstr\n";
	if ($query == '1') {
	}
	else {
		my $query = $dbh->do("INSERT INTO kingdomstat VALUES( '$kingid', '$tick', '$kingacres', '$kingscore', '0')") || die "Insert failed: $DBI::errstr\n";
	}
	$dbh->disconnect;

}

sub insert_provstat_into_mysql {
	my ($pname, $pscore, $pacres, $tick) = @_;
	
	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";

	my $query = $dbh->do("SELECT * FROM statistics WHERE pname='$pname' AND tick='$tick'") || die "Insert failed: $DBI::errstr\n";
	if ($query == '1') {
	}
	else {
		$query = $dbh->do("INSERT INTO statistics VALUES('$pname', '$tick', '$pacres', '$pscore', '0')") || die "Insert failed: $DBI::errstr\n";
	}
	$dbh->disconnect;
}

sub insert_kingdom_into_mysql {
	my ($kingid, $kcount, $kscore, $kacres, $tick) = @_;
	
	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";
	my $query = $dbh->do("SELECT * FROM kingdoms WHERE kingdomId='$kingid'") || die "Select failed: $DBI::errstr\n";
	if ($query == '1') {
		&insert_kingstat_into_mysql($kingid, $kscore, $kacres, $tick);
		my $query = $dbh->do("UPDATE kingdoms SET kingdomName='$kingid', kingdomCount='$kcount' WHERE kingdomId='$kingid'");
	} else {
		my $query = $dbh->do("INSERT INTO kingdoms VALUES( '$kingid', '$kingid', '$kcount')");
	}
	$dbh->disconnect;
}

sub insert_province_into_mysql {
	my ($pname, $prace, $pscore, $pacres, $pking, $tick) = @_;

	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";
	my $query = $dbh->do("SELECT * FROM provinces WHERE pname='$pname'") || die "Insert failed: $DBI::errstr\n";
	if ($query == '1') {
		insert_provstat_into_mysql($pname, $pscore, $pacres, $tick);
		my $query = $dbh->do("UPDATE provinces SET prace='$prace', pking='$pking' WHERE pname='$pname'");
	} else {
	
		my $query = $dbh->do("INSERT INTO provinces (pname, prace, pking) VALUES( '$pname', '$prace', '$pking')");
		&insert_provstat_into_mysql($pname, $pscore, $pacres, $tick);
	}
	$dbh->disconnect;
}

sub update_kd_placement {

	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";
	my $query = $dbh->prepare("SELECT * FROM kingdomstat WHERE time='$tick' ORDER BY score DESC") || die "Insert failed: $DBI::errstr\n";
	my $placement = 1;
	$query->execute;
	while(my @array=$query->fetchrow_array()) {
		my $query = $dbh->do("UPDATE kingdomstat SET lastp='$placement' WHERE kingdomId='$array[0]' AND time='$array[1]'");
		$placement++;		
	}
	$query->finish;
	$dbh->disconnect;

}

sub update_prov_placement {

	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";
	my $query = $dbh->prepare("SELECT * FROM statistics WHERE tick='$tick' ORDER BY score DESC") || die "Insert failed: $DBI::errstr\n";
	my $placement = 1;
	$query->execute;	
	while(my @array=$query->fetchrow_array()) {
		my $query = $dbh->do("UPDATE statistics SET lastp='$placement' WHERE pname='$array[0]' AND tick='$array[1]'");
		$placement++;		
	}
	$query->finish;
	$dbh->disconnect;

}

sub oppdater_tid {
	my ($sistetid) = @_;
	my $dbh = DBI->connect("DBI:mysql:tkocnet_stats:localhost","tkocnet_user","!tk\@oc#net\$") or die "Error connecting to database";
	my $query = $dbh->do("UPDATE diverse SET thevalue='$sistetid' WHERE thekey='sistetick'");
	$dbh->disconnect;
}



