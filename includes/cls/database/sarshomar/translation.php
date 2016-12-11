<?php
private function transtext()
{

	// ------------------------------------------------------------ Table commentdetails
	echo T_("commentdetails");      // Table commentdetails
	echo T_("commentdetail");       // commentdetail
	echo T_("minus");               // Enum minus
	echo T_("plus");                // Enum plus
	echo T_("user");                // user_id
	echo T_("comment");             // comment_id
	echo T_("type");                // type

	// ------------------------------------------------------------ Table comments
	echo T_("comments");            // Table comments
	echo T_("comment");             // comment
	echo T_("approved");            // Enum approved
	echo T_("unapproved");          // Enum unapproved
	echo T_("spam");                // Enum spam
	echo T_("deleted");             // Enum deleted
	echo T_("id");                  // id
	echo T_("post");                // post_id
	echo T_("author");              // comment_author
	echo T_("email");               // comment_email
	echo T_("url");                 // comment_url
	echo T_("content");             // comment_content
	echo T_("meta");                // comment_meta
	echo T_("status");              // comment_status
	echo T_("parent");              // comment_parent
	echo T_("visitor");             // visitor_id
	echo T_("modified");            // date_modified

	// ------------------------------------------------------------ Table exchangerates
	echo T_("exchangerates");       // Table exchangerates
	echo T_("exchangerate");        // exchangerate
	echo T_("up");                  // Enum up
	echo T_("down");                // Enum down
	echo T_("round");               // Enum round
	echo T_("enable");              // Enum enable
	echo T_("disable");             // Enum disable
	echo T_("expired");             // Enum expired
	echo T_("awaiting");            // Enum awaiting
	echo T_("filtered");            // Enum filtered
	echo T_("blocked");             // Enum blocked
	echo T_("from");                // from
	echo T_("to");                  // to
	echo T_("rate");                // rate
	echo T_("roundtype");           // roundtype
	echo T_("round");               // round
	echo T_("wagestatic");          // wagestatic
	echo T_("wage");                // wage
	echo T_("status");              // status
	echo T_("desc");                // desc
	echo T_("meta");                // meta
	echo T_("createdate");          // createdate
	echo T_("datemodified");        // datemodified
	echo T_("enddate");             // enddate

	// ------------------------------------------------------------ Table exchanges
	echo T_("exchanges");           // Table exchanges
	echo T_("exchange");            // exchange
	echo T_("exchangerate");        // exchangerate_id
	echo T_("valuefrom");           // valuefrom
	echo T_("valueto");             // valueto

	// ------------------------------------------------------------ Table filters
	echo T_("filters");             // Table filters
	echo T_("filter");              // filter
	echo T_("male");                // Enum male
	echo T_("female");              // Enum female
	echo T_("single");              // Enum single
	echo T_("marriade");            // Enum marriade
	echo T_("low");                 // Enum low
	echo T_("mid");                 // Enum mid
	echo T_("high");                // Enum high
	echo T_("illiterate");          // Enum illiterate
	echo T_("undergraduate");       // Enum undergraduate
	echo T_("graduate");            // Enum graduate
	echo T_("under diploma");       // Enum under diploma
	echo T_("diploma");             // Enum diploma
	echo T_("2 year college");      // Enum 2 year college
	echo T_("bachelor");            // Enum bachelor
	echo T_("master");              // Enum master
	echo T_("phd");                 // Enum phd
	echo T_("other");               // Enum other
	echo T_("-13");                 // Enum -13
	echo T_("14-17");               // Enum 14-17
	echo T_("18-24");               // Enum 18-24
	echo T_("25-30");               // Enum 25-30
	echo T_("31-44");               // Enum 31-44
	echo T_("45-59");               // Enum 45-59
	echo T_("60+");                 // Enum 60+
	echo T_("employee");            // Enum employee
	echo T_("unemployee");          // Enum unemployee
	echo T_("retired");             // Enum retired
	echo T_("owner");               // Enum owner
	echo T_("tenant");              // Enum tenant
	echo T_("homeless");            // Enum homeless
	echo T_("gender");              // gender
	echo T_("marrital");            // marrital
	echo T_("internetusage");       // internetusage
	echo T_("graduation");          // graduation
	echo T_("degree");              // degree
	echo T_("course");              // course
	echo T_("age");                 // age
	echo T_("agemin");              // agemin
	echo T_("agemax");              // agemax
	echo T_("range");               // range
	echo T_("country");             // country
	echo T_("province");            // province
	echo T_("city");                // city
	echo T_("employmentstatus");    // employmentstatus
	echo T_("housestatus");         // housestatus
	echo T_("religion");            // religion
	echo T_("language");            // language
	echo T_("industry");            // industry

	// ------------------------------------------------------------ Table logitems
	echo T_("logitems");            // Table logitems
	echo T_("logitem");             // logitem
	echo T_("critical");            // Enum critical
	echo T_("medium");              // Enum medium
	echo T_("title");               // logitem_title
	echo T_("desc");                // logitem_desc
	echo T_("meta");                // logitem_meta
	echo T_("priority");            // logitem_priority

	// ------------------------------------------------------------ Table logs
	echo T_("logs");                // Table logs
	echo T_("log");                 // log
	echo T_("expire");              // Enum expire
	echo T_("deliver");             // Enum deliver
	echo T_("logitem");             // logitem_id
	echo T_("data");                // log_data
	echo T_("meta");                // log_meta
	echo T_("status");              // log_status
	echo T_("createdate");          // log_createdate

	// ------------------------------------------------------------ Table notifications
	echo T_("notifications");       // Table notifications
	echo T_("notification");        // notification
	echo T_("read");                // Enum read
	echo T_("unread");              // Enum unread
	echo T_("user sender");         // user_idsender
	echo T_("title");               // notification_title
	echo T_("content");             // notification_content
	echo T_("meta");                // notification_meta
	echo T_("url");                 // notification_url
	echo T_("status");              // notification_status

	// ------------------------------------------------------------ Table options
	echo T_("options");             // Table options
	echo T_("option");              // option
	echo T_("cat");                 // option_cat
	echo T_("key");                 // option_key
	echo T_("value");               // option_value
	echo T_("meta");                // option_meta
	echo T_("status");              // option_status

	// ------------------------------------------------------------ Table polldetails
	echo T_("polldetails");         // Table polldetails
	echo T_("polldetail");          // polldetail
	echo T_("site");                // Enum site
	echo T_("telegram");            // Enum telegram
	echo T_("sms");                 // Enum sms
	echo T_("api");                 // Enum api
	echo T_("valid");               // Enum valid
	echo T_("invalid");             // Enum invalid
	echo T_("port");                // port
	echo T_("validstatus");         // validstatus
	echo T_("subport");             // subport
	echo T_("opt");                 // opt
	echo T_("txt");                 // txt
	echo T_("profile");             // profile
	echo T_("insertdate");          // insertdate

	// ------------------------------------------------------------ Table pollstats
	echo T_("pollstats");           // Table pollstats
	echo T_("pollstat");            // pollstat
	echo T_("total");               // total
	echo T_("result");              // result

	// ------------------------------------------------------------ Table postfilters
	echo T_("postfilters");         // Table postfilters
	echo T_("postfilter");          // postfilter
	echo T_("filter");              // filter_id

	// ------------------------------------------------------------ Table posts
	echo T_("posts");               // Table posts
	echo T_("post");                // post
	echo T_("open");                // Enum open
	echo T_("closed");              // Enum closed
	echo T_("stop");                // Enum stop
	echo T_("pause");               // Enum pause
	echo T_("trash");               // Enum trash
	echo T_("publish");             // Enum publish
	echo T_("draft");               // Enum draft
	echo T_("schedule");            // Enum schedule
	echo T_("violence");            // Enum violence
	echo T_("pornography");         // Enum pornography
	echo T_("poll");                // Enum poll
	echo T_("survey");              // Enum survey
	echo T_("public");              // Enum public
	echo T_("private");             // Enum private
	echo T_("language");            // post_language
	echo T_("title");               // post_title
	echo T_("slug");                // post_slug
	echo T_("url");                 // post_url
	echo T_("content");             // post_content
	echo T_("meta");                // post_meta
	echo T_("type");                // post_type
	echo T_("comment");             // post_comment
	echo T_("count");               // post_count
	echo T_("order");               // post_order
	echo T_("status");              // post_status
	echo T_("parent");              // post_parent
	echo T_("publishdate");         // post_publishdate
	echo T_("survey");              // post_survey
	echo T_("gender");              // post_gender
	echo T_("sarshomar");           // post_sarshomar
	echo T_("privacy");             // post_privacy
	echo T_("rank");                // post_rank
	echo T_("count");               // comment_count

	// ------------------------------------------------------------ Table ranks
	echo T_("ranks");               // Table ranks
	echo T_("rank");                // rank
	echo T_("member");              // member
	echo T_("report");              // report
	echo T_("vot");                 // vot
	echo T_("like");                // like
	echo T_("faiv");                // faiv
	echo T_("skip");                // skip
	echo T_("view");                // view
	echo T_("other");               // other
	echo T_("sarshomar");           // sarshomar
	echo T_("ago");                 // ago
	echo T_("value");               // value

	// ------------------------------------------------------------ Table terms
	echo T_("terms");               // Table terms
	echo T_("term");                // term
	echo T_("language");            // term_language
	echo T_("type");                // term_type
	echo T_("title");               // term_title
	echo T_("slug");                // term_slug
	echo T_("url");                 // term_url
	echo T_("desc");                // term_desc
	echo T_("meta");                // term_meta
	echo T_("parent");              // term_parent

	// ------------------------------------------------------------ Table termusages
	echo T_("termusages");          // Table termusages
	echo T_("termusage");           // termusage
	echo T_("posts");               // Enum posts
	echo T_("products");            // Enum products
	echo T_("attachments");         // Enum attachments
	echo T_("files");               // Enum files
	echo T_("comments");            // Enum comments
	echo T_("term");                // term_id
	echo T_("termusage");           // termusage_id
	echo T_("foreign");             // termusage_foreign
	echo T_("order");               // termusage_order

	// ------------------------------------------------------------ Table transactionitems
	echo T_("transactionitems");    // Table transactionitems
	echo T_("transactionitem");     // transactionitem
	echo T_("real");                // Enum real
	echo T_("gift");                // Enum gift
	echo T_("prize");               // Enum prize
	echo T_("transfer");            // Enum transfer
	echo T_("yes");                 // Enum yes
	echo T_("no");                  // Enum no
	echo T_("title");               // title
	echo T_("caller");              // caller
	echo T_("unit");                // unit_id
	echo T_("minus");               // minus
	echo T_("plus");                // plus
	echo T_("autoverify");          // autoverify
	echo T_("forcechange");         // forcechange

	// ------------------------------------------------------------ Table transactions
	echo T_("transactions");        // Table transactions
	echo T_("transaction");         // transaction
	echo T_("transactionitem");     // transactionitem_id
	echo T_("budgetbefor");         // budgetbefor
	echo T_("budget");              // budget
	echo T_("exchange");            // exchange_id
	echo T_("user_id");             // related_user_id
	echo T_("parent");              // parent_id
	echo T_("finished");            // finished

	// ------------------------------------------------------------ Table units
	echo T_("units");               // Table units
	echo T_("unit");                // unit

	// ------------------------------------------------------------ Table users
	echo T_("users");               // Table users
	echo T_("user");                // user
	echo T_("active");              // Enum active
	echo T_("deactive");            // Enum deactive
	echo T_("removed");             // Enum removed
	echo T_("filter");              // Enum filter
	echo T_("mobile");              // user_mobile
	echo T_("email");               // user_email
	echo T_("pass");                // user_pass
	echo T_("displayname");         // user_displayname
	echo T_("meta");                // user_meta
	echo T_("status");              // user_status
	echo T_("permission");          // user_permission
	echo T_("createdate");          // user_createdate
	echo T_("parent");              // user_parent
	echo T_("validstatus");         // user_validstatus

}
?>