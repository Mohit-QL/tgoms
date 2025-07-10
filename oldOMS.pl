#!/usr/bin/perl
use CGI;
use CGI::Carp qw(fatalsToBrowser);

$basedir = 'e:\domains\r\rtin-mini.ch\user\htdocs\cgi-bin\oms';
$filesep = '\\';
$formurl = '/cgi-bin/oldOMS.pl';
$fileurl = '/cgi-bin/oms/files';
$omsbasicdir = '/cgi-bin/oms/';
$filedir = "$basedir$filesep" . 'files';

unless (-e 'myserver') {
  $basedir = '/home/content/t/g/s/tgserver/html/cgi/oms';
  $filesep = '/';
  $formurl = '/cgi/oldOMS.pl';
  $fileurl = '/oms/files';
  $omsbasicdir = '/oms/';
  $filedir = "/home/content/t/g/s/tgserver/html/oms/files";

  $basedir = '/usr/home/turnergr/public_html/oms';
  $filesep = '/';
  $formurl = '/oldOMS.pl';
  $fileurl = '/files';
  $omsbasicdir = '/';
  $filedir = "$basedir/files";
}

$offlinefile = "$basedir$filesep" . 'offline.txt';
$printtemplatefile = 'omsadmin13.txt';

$detailsdir = "$basedir$filesep" . 'details';
$orderdir = "$basedir$filesep" . 'orders';
$queuedir = "$basedir$filesep" . 'queue';

$billeddir = "$orderdir$filesep" . 'billed';
$paiddir = "$orderdir$filesep" . 'paid';
$completeddir = "$orderdir$filesep" . 'completed';
$artcompleteddir = "$orderdir$filesep" . 'artcompleted';

$default{'rep'} = 'tg-sales';
$default{'artist'} = 'tg-art';
$superadmin = 'turnerbros';

$sortfield1 = 'category';
$sortfield2 = 'type';

$displayfield1 = 'billtoclient';
$displayfield2 = 'billtoproject';
$displayfield3 = 'duedate';

$submitorderbutton = 'ADD ORDER';
@accounttypes = ('artist', 'crew', 'rep', 'admin');

$datetitle{'duedate'} = 'Order Due Date';
$datetitle{'scheduledtoprint'} = 'Scheduled to<br>Print Date';
$datetitle{'artdue'} = 'Art Due Date';
$datetitle{'orderdue'} = 'Order Date';

$defaultreversesort = 'duedate';

chdir($basedir) || die("No directory: $basedir\n\n");

foreach $foo ('header', 'footer') {
  open(FILE, $foo . '.txt') || die ("No $foo file.\n\n");
    while(<FILE>) { ${$foo} .= $_ }
  close(FILE);
}

print "Content-type: text/html\n\n";

$cgi = new CGI;
$enterpass1 = $cgi->param('pass1');
$enterpass2 = $cgi->param('pass2');

chdir($detailsdir) || die("No directory: $detailsdir\n\n");
@foo = <$enterpass1.*.initials>;
open(FILE, $foo[0]);
  chomp($pass2 = <FILE>);
close(FILE);
$pass1 = $enterpass1;

foreach (@accounttypes) { $usertype{$_} = 'true' if -e "$enterpass1.$_" }
$usertype{'superadmin'} = true if $enterpass1 eq $superadmin;

if ($enterpass2 eq $pass2 && length($enterpass2) > 0 && length($enterpass1) > 0 && ( (-e $offlinefile && $enterpass1 eq $superadmin) || !($cgi->param('logout') || -e $offlinefile))) {
  chdir ($queuedir) || die("No directory: $queuedir.\n\n");
  foreach (<*>) { unlink if (/\.$enterpass1$/i || -M > (5/1440) ) }

  $addorder = $cgi->param('addorder');
  $sort = $cgi->param('sortorders');
  $search = $cgi->param('searchorders');
  $controlpanel = $cgi->param('controlpanel');
  $artboard = $cgi->param('artboard');
  $formid = $cgi->param('formid');
  $printone = $cgi->param('printone');
  $printmultiple = $cgi->param('printmultiple');
  $foo = $archived = $cgi->param('archived');
    $foo =~ s/\W//sg;
    ${'form_archived_' . $foo . '_select'} = ' SELECTED';
  $editonly = $cgi->param('editonly');

  $artcompleted = $cgi->param('artcompleted');
  ${'form_artcompleted_' . $artcompleted . '_select'} = ' SELECTED';
  $artboard = '12' if $artcompleted;
  $usertype{'artcompleted'} = '12' if $artcompleted;

  if ($addorder) {
    $saveinfo = $cgi->param('saveclient');
    $billingtoclient = $cgi->param('billtoclient');
    $billingtoclient =~ s/[^\w ]//sg;
    if ($saveinfo) {
      chdir($detailsdir) || die("No directory: $detailsdir\n\n");
      open(FILE, ">$billingtoclient.client") || die("File handling error 1.\n\n");
        foreach ('billtoclient', 'billtocontact', 'billtoaddress', 'billtocity', 'billtostate', 'billtozip', 'billtophone', 'billtoemail') {
          print FILE $cgi->param($_) . "\n";
        }
      close(FILE);
    }

    chdir($orderdir) || die ("No directory: $orderdir\n\n");
    open (FILE, "storetemplate.txt") || die ("No store template file.\n\n");
      while (<FILE>) {
        chomp;
        $mandatory = '';
        $mandatory = $1 if s/^(\*)//;
        $foo = $cgi->param($_);
        $error = '<li>Mandatory Field Missing.</li>' if ($mandatory eq '*' && length($foo) == 0); 
        $foo =~ s/\n//sig;
        push(@neworder, "$_:$foo"); 
        s/\W/-/sig;
        ${'form_' . $_} = $foo;
        $foo =~ s/\W/-/sig;

        ${'form_' . $_} =~ s/&/&amp;/sig;
        ${'form_' . $_} =~ s/"/&quot;/sig;
        ${'form_' . $_} =~ s/</&lt;/sig;
        ${'form_' . $_} =~ s/>/&gt;/sig;
        ${'form_' . $_} =~ s/  / &nbsp;/sig;

        ${'form_' . $_ . '_' . $foo . '_radio'} = ' CHECKED';
        ${'form_' . $_ . '_' . $foo . '_select'} = ' SELECTED';
      }
    close(FILE);

    if ($error) {
      $error = "<font color=#ff0000><ul>$error</ul></font>";
      ${'form_changeclient_' . $cgi->param('changeclient') . '_select'} = ' SELECTED';
      chdir($detailsdir) || die("No directory: $detailsdir\n\n");
      foreach $foo ('client', 'rep', 'artist') {
        $number = 0;
        foreach (<*.$foo>) {
          $name = $1 if /^(.*?)\.$foo/si;
          if ($foo eq 'client') {
            open (FILE, $_) || die("File handling error 2.\n\n");
              foreach $bar ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
                chomp($_ = <FILE>);
                s/'/\\'/sg;
                push(@{$bar}, $_) if $foo eq 'client';
              }
            close(FILE);
            $clientlist .= "<option value='" . $number . "' " . ${'form_changeclient_' . $number . '_select'} . ">$name</option>";
            $number++;
          }
          else {
            if (${'form_' . $foo . 'name'} eq $name) { 
              ${$foo . 'list'} .= "<option value='$name' selected>$name</option>";
            }
            else {
              ${$foo . 'list'} .= "<option value='$name'>$name</option>";
            }
          }
        }
        ${$foo . 'list'} =~ s/(<option value='$default{$foo}')/$1 selected/si unless ${$foo . 'list'} =~ /' selected>/i;
      }

      foreach $foo ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
        $clientjavascript .= "  var $foo = new Array('" . join("', '", @{$foo}) . "');\n";
      }
      $outputfile = 'omsadmin2.txt';
    }
    else {

      if ($formid) {
        $ordernumber = $formid;
      }
      else {
        {
          @foo = <*.nextorder>;
          die("No next order file.\n\n") unless $foo[0] =~ s/^(\d+)\..*$/$1/;
          $bar = $foo[0] + 1;
          $bar = '0' x (6 - length($bar)) . $bar;
          rename ("$foo[0].nextorder", "$bar.nextorder") || next
        }
        $ordernumber = $foo[0];
      }
      open (FILE, ">$ordernumber.order") || die("File handling error 3.\n\n");
        print FILE join("\n", @neworder);
      close(FILE);
      foreach ('billed', 'completed', 'paid') { unlink("${$_ . 'dir'}$filesep$ordernumber.order") if -e "${$_ . 'dir'}$filesep$ordernumber.order" }

      if ($cgi->param('stageComplete')) { 
        if    ($cgi->param('stagePaid'))   { rename("$ordernumber.order", "$paiddir$filesep$ordernumber.order") || die("Unable to rename.\n\n") }
        elsif ($cgi->param('stageBilled')) { rename("$ordernumber.order", "$billeddir$filesep$ordernumber.order") || die("Unable to rename.\n\n") }
        else 				   { rename("$ordernumber.order", "$completeddir$filesep$ordernumber.order") || die("Unable to rename.\n\n") }
      }

      $outputfile = 'omsadmin3.txt';

      my $file = $cgi->param('file');
      if ($file) {
        chdir($filedir) || die("No directory: $filedir.\n\n");
        $file=~m/^(.*(\\|\/))?(.*)\.(.+)$/;  
        $ext = $4;
        binmode($file);
        open(FILE, ">$ordernumber" . ".$ext") or die $!;
          binmode FILE;
          while(<$file>) { print FILE }
        close(FILE);
      }
    }
  }
  elsif ($sort || $artboard || $printmultiple) {
    chdir($orderdir) || die ("No directory: $orderdir\n\n");

    $orderlisttemplatefile = 'orderlisttemplate.txt';
    $orderlisttemplatefile = 'artlisttemplate.txt' if $artboard;

    open (FILE, $orderlisttemplatefile) || die("No order list template file.\n\n");
      $orderlisttemplate = join('', <FILE>);
    close(FILE);

    if ($sort eq '123456' || $artboard || $printmultiple) {

    $archived = 'order' unless $archived;
    @archivedlist = split /:/, $archived;

      foreach $archived ( @archivedlist ) {
        chdir(${$archived . 'dir'}) if $archived;
        $artboarduser = $cgi->param('artboarduser');
        $artboarduser = $enterpass1 unless $artboarduser;
        foreach (<*.order>) {
          $name = $1 if /^(.*?)\.order/i;
          open (FILE, $_) || die ("File handling error 4.\n\n");
            $foo = join('', <FILE>);
          close(FILE);

          ($filter, $filtervalue, $filtervaluecopy) = ($cgi->param('filter'), $cgi->param('filterstring'), $cgi->param('filterstring') );
          ($filter2, $filtervalue2, $filtervalue2copy) = ($cgi->param('filter2'), $cgi->param('filterstring2'), $cgi->param('filterstring2') );
          ($reversesort, $reversesortcopy) = ($cgi->param('reversesort'), $cgi->param('reversesort'));

          ($reversesort, $reversesortcopy) = ($defaultreversesort, $defaultreversesort) unless $reversesort;
          $filtervalue2copy =~ s/\W//sg;
          $filtervaluecopy =~ s/\W//sg;
          $reversesortcopy =~ s/\W//sg;
          ${'form_filterstring2_' . $filtervalue2copy . '_select'} = ' SELECTED' if $filtervalue2;
          ${'form_filterstring_' . $filtervaluecopy . '_select'} = ' SELECTED' if $filtervalue;
          ${'form_reversesort_' . $reversesortcopy . '_select'} = ' SELECTED' if $reversesort;

          
if (
 (($foo =~ /^$filter:.*?$filtervalue.*?$/mi || $filter eq '') && $sort && (( $filtervalue2 eq 'Mock-up (Complete)' && $foo =~ /^$filter2:Mock-up \(Complete\)$/mi  ) || $foo =~ /^$filter2:.*?$filtervalue2$/mi || $filtervalue2 eq '') ) || ($foo =~ /^artistname:$artboarduser$/mi && $artboard)) {
            foreach ('sortfield1', 'sortfield2', 'displayfield1', 'displayfield2', 'displayfield3', 'reversesort') {
              ${$name}{${$_}} = $1 if $foo =~ /^${$_}:(.*?)$/mi;
            }
            ${$name}{'rev_' . $reversesort} = $1 if $foo =~ /^$reversesort:(.*?)$/mi;
            ${$name}{'rev_' . $reversesort} =~ m#^(\d+)/(\d+)/(\d+)$#;
            ${$name}{'rev_' . $reversesort} = "$3$1$2";

            ${$name}{'linktofile'} = '[no&nbsp;uploaded&nbsp;art]';
            @foo = <$filedir$filesep$name.*>;

            $foo[0] =~ s/^(.*(\\|\/))?(.*)\.(.+)$/$3\.$4/si; 
            ${$name}{'linktofile'} = "<a href='$fileurl/$foo[0]' target=" . localtime . "><font color=000000>uploaded art</a>" if $foo[0];

            @stages = ('foo', 'stageOrderGoods', 'stageGoodsReceived', 'stageArtApproval', 'stageOrderStaging', 'stagePrintingStitching', 'stageComplete', 'stageBilled', 'stagePaid');
            @stages = ('foo', 'initialsinprogress', 'initialscomplete', 'initialssenttoapprove', 'initialsrevisions', 'initialsapproved', 'initialssepsdone' ) if $artboard;

            for ($i = 1; $i < 9; $i++) {
              ${$name}{'stage' . $i . 'gif'} = $omsbasicdir . 'redsphere.gif';
              ${$name}{'stage' . $i . 'gif'} = $omsbasicdir . 'bluesphere.gif' if $foo =~ /^$stages[$i]:\w+/mi;
            }

            ${$name}{'name'} = $name;
            ${$name}{'archived'} = $archived;
            if ($artcompleted) { push(@ordernames, $name) if ( $foo =~ /^initialshours:\w+/mi || $foo =~ /^initialsprooffilm:\w+/mi ) }
            else 	       { push(@ordernames, $name) unless ($artboard && ( $foo =~ /^initialshours:\w+/mi || $foo =~ /^initialsprooffilm:\w+/mi )) }
          }
        }
      }

      @ordernames = sort { ${$a}{'rev_' . $reversesort} <=> ${$b}{'rev_' . $reversesort} || ${$a}{$sortfield1} cmp ${$b}{$sortfield1} || ${$a}{$sortfield2} cmp ${$b}{$sortfield2} } @ordernames;

      $datetitle = $datetitle{$reversesort};

      foreach (@ordernames) {
        $orderlist .= $orderlisttemplate;
        $orderlist =~ s/<!--DATA-(.*?)-->/${$1}/sig;
        $orderlist =~ s/<!--ORDERDATA-(.*?)-->/${$_}{$1}/sig;
      }
      $nosortresults = true if length($ordernames[0]) < 1;
    }
    else {
      $nosortresults = true;
    }
    if ($artboard) {
      chdir($detailsdir) || die("No directory: $detailsdir\n\n");
      foreach (<*.artist>) {
        $name = $1 if /^(.*?)\.artist$/si;
        if   ($name eq $artboarduser) { $artistlist .= "<option value='$name' selected>$name</option>" }
        else                          { $artistlist .= "<option value='$name'>$name</option>" }
      }
      $outputfile = 'omsadmin17.txt' if $artboard;
    }
    elsif ($printmultiple) {
      chdir($basedir) || die("No directory: $basedir\n\n");
      open(FILE, $printtemplatefile) || die("No file: $printtemplatefile\n\n");
        $printtemplate = join('', <FILE>);
      close(FILE);
      $printtemplate =~ s/<!--DATA-opencutout-->/<!--/sig;
      $printtemplate =~ s/<!--DATA-closecutout-->/-->/sig;
      $totalprinttemplate = $1 if $printtemplate =~ s/(^.*?<body.*?>)//si;
      $templateending = $1 if $printtemplate =~ s#(</body>.*?$)##sig;

      chdir($orderdir) || die("No directory: $orderdir\n\n");
      foreach (@ordernames) {
        $newprinttemplate = $printtemplate;
        $newprinttemplate =~ s/<!--DATA-formid-->/${$_}{'name'}/sig;
        $newprinttemplate =~ s/<!--DATA-orderformid-->/Order Number: ${$_}{'name'}<p>/sig;
        open(FILE, "${$_}{'name'}.order") || die("Form not Found 1\n\n");
          while (<FILE>) {
            chomp;
            /^(.*?):(.*?)$/;
            ($foo, $bar) = ($1, $2);
            $foo =~ s/\W/-/sig;
            $bar2 = $bar;
            $bar =~ s/\W/-/sig;
            $bar2 =~ s/&/&amp;/sig;
            $bar2 =~ s/"/&quot;/sig;
            $bar2 =~ s/</&lt;/sig;
            $bar2 =~ s/>/&gt;/sig;
            $bar2 =~ s/  / &nbsp;/sig;
            $newprinttemplate =~ s/<!--DATA-form_$foo-->/$bar2/sig;
            $newprinttemplate =~ s/<!--DATA-form_$bar_radio-->/ CHECKED/sig;
            $newprinttemplate =~ s/<!--DATA-form_$bar_select-->/ SELECTED/sig;
          }
        close(FILE);
        $newprinttemplate =~ s/<!--DATA-.*?-->//sig;
        $totalprinttemplate .= $newprinttemplate . "\n<DIV style='page-break-after:always'></DIV>";
      }

      print $totalprinttemplate . $templateending;
#      $outputfile = 'omsadmin4.txt';
    }
    else {
      $outputfile = 'omsadmin4.txt';
    }
  }
  elsif ($cgi->param('deleteuser')) {
    foreach ('searchbytype', 'olduserartist', 'olduserrep', 'oldusercrew', 'olduseradmin') {
      ${$_} = $cgi->param($_);
    }
    $outputfile = 'omsadmin14.txt';
  }
  elsif ($controlpanel) {
    if ($cgi->param('addeditdelete')) {
      chdir($detailsdir) || die("No directory: $detailsdir.\n\n");
      if ($cgi->param('add')) {
        $newusername = $cgi->param('newuser');
        $newuserinitials = uc($cgi->param('newuserinitials'));
        $error =  "<li>Username should be one word, contain letters and numbers only and be 5 - 20 characters in length.</li>" unless $newusername =~ /^(\w|\-){5,20}$/s;
        $error .= "<li>Username exists.</li>" if ((-e "$newusername.artist") || (-e "$newusername.rep") || (-e "$newusername.crew") || (-e "$newusername.admin") );
        @foo = <*.$newuserinitials.initials>;
        $error .= "<li>Initials in use.</li>" if (-e $foo[0]);
        $error .= "<li>Initials should be 2-4 letters long.</li>" unless $newuserinitials =~ /^\w{2,4}$/s;
        $error = "<font color=#ff0000><ul>$error</ul></font>" if $error;

        unless ($error) {
          open (FILE, ">$newusername.$newuserinitials.initials") || die("File handling error 6.\n\n");
            print FILE "password";
          close (FILE);
          foreach $type (@accounttypes) {
            if ($cgi->param("newuser$type")) {
	      open(FILE, ">$newusername.$type") || die("File handling error 7.\n\n");
              close(FILE);
            }
          }
        }
      }
      elsif ($cgi->param('edit')) {
        if ($cgi->param('edituser')) {
          $edituser = $cgi->param('edituser');
          @foo = <$edituser.*.initials>;
          open(FILE, $foo[0]) || die("File handling error 8.\n\n");
            chomp($newpassword = <FILE>);
          close(FILE);
        }

        $newusername = $cgi->param('newusername');
        $newuserinitials = uc($cgi->param('newuserinitials'));
        $error =  "<li>Username should be one word, contain letters and numbers only and be 5 - 20 characters in length.</li>" unless $newusername =~ /^(\w|\-){5,20}$/s;
        $error .= "<li>Username exists.</li>" if (((-e "$newusername.artist") || (-e "$newusername.rep") || (-e "$newusername.crew") || (-e "$newusername.admin") ) && $newusername ne $cgi->param('edituser') );
        @temp = <*.$newuserinitials.initials>;
        $error .= "<li>Initials in use.</li>" if (-e $temp[0] && $foo[0] ne $temp[0]);
        $error .= "<li>Initials should be 2-4 letters long.</li>" unless $newuserinitials =~ /^\w{2,4}$/s;
        $error = "<font color=#ff0000><ul>$error</ul></font>" if $error;

        if ($cgi->param('edituser') && (!($error))) {
          if ($cgi->param('resetpassword')) {
            $newpassword = 'password';
            $enterpass2 = $newpassword if $enterpass1 eq $edituser;
          }
          foreach $type (@accounttypes) { unlink "$edituser.$type" if -e "$edituser.$type" }

          unlink $foo[0] || die("File handling error.\n\n");

          open (FILE, ">$newusername.$newuserinitials.initials") || die("File handling error 9.\n\n");
            print FILE "$newpassword";
          close (FILE);
          foreach $type (@accounttypes) {
            if ($cgi->param('newuser' . $type)) {
	      open(FILE, ">$newusername.$type") || die("File handling error 10.\n\n");
                print FILE "$newpassword\n$newuserinitials";
              close(FILE);
            }
          }       
        }
        else {
          $edituser = $cgi->param('olduser' . $cgi->param('searchbytype'));
          if ($edituser) { $error = '' } else { $edituser = $cgi->param('edituser') }
          foreach $type (@accounttypes) { ${$type . 'checked'} = ' CHECKED' if -e "$edituser.$type" }
          unless ($newuserinitials) {
            @foo = <$edituser.*.initials>;
            $olduserinitials = $foo[0];
            $olduserinitials =~ s/^$edituser\.(.*?)\.initials$/$1/si;
          }
          $outputfile = 'omsadmin7.txt';
        }
      }
      elsif ($cgi->param('delete')) {
        chdir($detailsdir) || die("File handling error.\n\n");
        $edituser = $cgi->param('olduser' . $cgi->param('searchbytype'));
        foreach $file ( <$edituser.*> ) { unlink $file }
        $error = "Account ($edituser) deleted.";
      }

      foreach $type (@accounttypes) {
        foreach (<*.$type>) { s/\.$type$//si; ${$type . 'list'} .= "<option value='$_'>$_</option>"; }
      }
      $outputfile = 'omsadmin6.txt' unless $outputfile;
    }
    elsif ($cgi->param('changepassword')) {
      $newpassword = $cgi->param('newpassword');
      $error =  '<li>Password should be one word, contain letters and numbers only and be 5 - 20 characters in length.</li>' unless $newpassword  =~ /^(\w|\-){5,20}$/s;
      $error .= '<li>Passwords do not match.</li>' unless $newpassword eq $cgi->param('newpassword2');
      if ($newpassword && !($error)) {
        @foo = glob($detailsdir . $filesep . $enterpass1 . '.*.initials');
        open (FILE, ">$foo[0]") || die ("File handling error: $foo[0].\n\n");
          print FILE $newpassword;
        close(FILE);
        $enterpass2 = $newpassword;     
        $outputfile = 'omsadmin5.txt';    
      }
      else {     
        $error = '' unless $newpassword;
        $outputfile = 'omsadmin8.txt';
      }
    }
    elsif ($cgi->param('clientdata')) {
      chdir($detailsdir) || die("No directory: $detailsdir\n\n");
      if ($cgi->param('changeclient')) {
        @foo = <*.client>;
        unlink($foo[$cgi->param('changeclient')]) || die ("File handling error.\n\n");
        $error = 'Client Information Deleted';
      }
      $number = 0;
      foreach (<*.client>) {
        $name = $1 if /^(.*?)\.client/si;
        open (FILE, $_) || die("File handling error 15.\n\n");
          foreach $bar ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
            chomp($_ = <FILE>);
            s/'/\\'/sg;
            push(@{$bar}, $_);
          }
        close(FILE);
        $clientlist .= "<option value='$number'>$name</option>";
        $number++;
      }
      foreach $foo ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
        $clientjavascript .= "  var $foo = new Array('" . join("', '", @{$foo}) . "');\n";
      }
      $outputfile = 'omsadmin18.txt';
    }
    else {
      $outputfile = 'omsadmin5.txt';
    }
  }
  elsif ($search) {
    $outputfile = 'omsadmin9.txt';
  }
  elsif ($cgi->param('deletelink')) {
    $outputfile = 'omsadmin10.txt';
  }
  elsif ($cgi->param('deletelinkfinal')) {
    chdir($orderdir) || die("No directory: $orderdir\n\n");
    unlink("$formid.order") || die("File handling error 13.\n\n");
    chdir($filedir) || die("No directory: $filedir\n\n");
    foreach (<$formid.*>) { unlink }
    $outputfile = 'omsadmin11.txt';
  }
  elsif ($cgi->param('justloggedin')) {
    $outputfile = 'omsadmin16.txt';
  }

#############################

  else {
    if ($formid && !($cgi->param('neworder')) ) {
      chdir($queuedir) || die("No directory: $queuedir\n\n");
      @foo = <$formid.*>;
      if ($foo[0] && !($editonly)) {
        $outputfile = 'omsadmin12.txt';
        $queue = 'true';
      }
      else {
        unless ($editonly) {
          open(FILE, ">$formid.$enterpass1") || die("File handling error 14.\n\n");
          close(FILE);
        }
        chdir($orderdir) || die("No directory: $orderdir\n\n");
        chdir(${$archived . 'dir'}) if $archived;
        $submitorderbutton = 'UPDATE ORDER';
        open(FILE, "$formid.order") || die("Form not Found 2\n\n");
          while (<FILE>) {
            chomp;
            /^(.*?):(.*?)$/;
            ($foo, $bar) = ($1, $2);
            $foo =~ s/\W/-/sig;
            ${'form_' . $foo} = $bar;
            $bar =~ s/\W/-/sig;

            ${'form_' . $foo} =~ s/&/&amp;/sig;
            ${'form_' . $foo} =~ s/"/&quot;/sig;
            ${'form_' . $foo} =~ s/</&lt;/sig;
            ${'form_' . $foo} =~ s/>/&gt;/sig;
            ${'form_' . $foo} =~ s/  / &nbsp;/sig;

            ${'form_' . $foo . '_' . $bar . '_radio'} = ' CHECKED';
            ${'form_' . $foo . '_' . $bar . '_select'} = ' SELECTED';
          }
        close(FILE);
        $opencutout = '<!--';
        $closecutout = '-->';
        $orderformid = "Order Number: $formid<p>";
        $usertype{'formid'} = 'true' unless $editonly;
        $usertype{'editonly'} = 'true' if $editonly;
      }
    }
    else {
      chdir($orderdir) || die("No directory: $orderdir\n\n");
      chdir(${$archived . 'dir'}) if $archived;
      open(FILE, 'defaultform.txt') || die("No default form layout\n\n");
        while (<FILE>) { chomp; /^(.*?):(.*?)$/; ${"form_$1"} = $2 }
      close(FILE);
      (undef, undef, undef, $foo4, $foo5, $foo6) = localtime;
      $foo4 = '0' . $foo4 if length($foo4) < 2;
      $foo5++;
      $foo5 = '0' . $foo5 if length($foo5) < 2;
      $foo6 =~ s/^1(\d\d)$/$1/;
      $form_orderdue = "$foo5\/$foo4\/20$foo6";
    }

    unless ($queue) {
      chdir($detailsdir) || die("No directory: $detailsdir\n\n");
      foreach $foo ('client', 'rep', 'artist') {
        $number = 0;
        foreach (glob("*.$foo")) {
          $name = $1 if /^(.*?)\.$foo/si;
          if ($foo eq 'client') {
            open (FILE, $_) || die("File handling error 15.\n\n");
              foreach $bar ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
                chomp($_ = <FILE>);
                s/'/\\'/sg;
                push(@{$bar}, $_) if $foo eq 'client';
              }
            close(FILE);
            $clientlist .= "<option value='" . $number . "' " . ${'form_changeclient_' . $number . '_select'} . ">$name</option>";
            $number++;
          }
          else {
            if (${'form_' . $foo . 'name'} eq $name) { 
              ${$foo . 'list'} .= "<option value='$name' selected>$name</option>";
              ${"form_$foo" . "_value"} = $name;
            }
            else {
              ${$foo . 'list'} .= "<option value='$name'>$name</option>";
            }
          }
        }
        ${$foo . 'list'} =~ s/(<option value='$default{$foo}')/$1 selected/si unless ${$foo . 'list'} =~ /' selected>/i;
      }
      foreach $foo ('clientclient', 'clientcontact', 'clientaddress', 'clientcity', 'clientstate', 'clientzip', 'clientphone', 'clientemail') {
        $clientjavascript .= "  var $foo = new Array('" . join("', '", @{$foo}) . "');\n";
      }
      $outputfile = 'omsadmin2.txt';
      $outputfile = $printtemplatefile if $printone;
    }
  }
}
elsif (-e $offlinefile && length($enterpass1) > 0) {
  $outputfile = 'omsadmin15.txt';
}
else {
  if ($cgi->param('logout')) {
    ($enterpass1, $enterpass2) = ('', '');
  }
  elsif ($enterpass1 || $enterpass2) {
    $error = '<font color=#ff0000#>ERROR: Incorrect username/password.<p></font>';
  }
  $outputfile = 'omsadmin1.txt';
}

if ($outputfile) {
  foreach $foo ('header', 'footer') {
    ${$foo} =~ s/<!--DATA-(.*?)-->/${$1}/sig;
    foreach $type (@accounttypes, 'formid') {
      if ($usertype{$type}) { ${$foo} =~ s/<!--ONLY-$type\-(.*?)-->/$1/sig } else { ${$foo} =~ s/<!--ONLY-$type\-.*?-->//sig }
    }
  }

  chdir($basedir) || die("No directory: $basedir\n\n");
  open(FILE, $outputfile) || die("No file: $outputfile.\n\n");
    while(<FILE>) {
      if ($nosortresults && /<!--noresultsstart-->/si) {
        until (/<!--noresultsend-->/si) { $_ = <FILE> }
        print '<p align=left>[ No Matches ]<p>';
      }
      else {
        s/<!--DATA-(.*?)-->/${$1}/sig;
        foreach $type (@accounttypes, 'formid', 'superadmin', 'artcompleted', 'editonly') {
          if ($usertype{$type}) { s/<!--ONLY-$type\-(.*?)-->/$1/sig } else { s/<!--ONLY-$type\-.*?-->//sig }
        }
        print;
      }
    }
  close(FILE);
}