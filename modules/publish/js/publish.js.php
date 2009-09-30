<script type="text/javascript">

function saveArticleOrder()
{
	new Ajax.Request("<?php echo BUGScontext::getTBGPath(); ?>modules/publish/publish.php", 
	{
		method: "post",
		parameters: { articles: Sortable.serialize("article_list") },
		onSuccess: function (ordersaved)
		{
			Effect.Appear('article_order_saved');
			Effect.Fade('article_order_saved', { duration: 6 });
		}
	});
}

function removeBillboardPost(pid)
{
	new Ajax.Request("<?php echo BUGScontext::getTBGPath(); ?>modules/publish/publish.php", 
	{
		method: "post",
		parameters: { p_id: pid },
		onComplete: function (postremoved)
			{
				Effect.Fade('billboardpost_' + pid);
			}
	});
}

function addBillboardPost()
{
	var params = Form.serialize('add_new_post_form');
	bid = $('post_text_billboard').getValue();
	new Ajax.Updater('billboard_' + bid, "<?php echo BUGScontext::getTBGPath(); ?>modules/publish/billboard.php", 
	{
		method: "post",
		parameters: params,
		insertion: Insertion.Top,
		onSuccess: function (postedtext)
			{
				Form.reset('add_new_post_form');
				$('post_new_text').toggle();
			}
	});
}

function addBillboardLink()
{
	var params = Form.serialize('add_new_link_form');
	bid = $('post_link_billboard').getValue();
	new Ajax.Updater('billboard_' + bid, "<?php echo BUGScontext::getTBGPath(); ?>modules/publish/billboard.php", 
	{
		method: "post",
		parameters: params,
		insertion: Insertion.Top,
		onSuccess: function (postedlink)
			{
				Form.reset('add_new_link_form');
				$('post_new_link').toggle();
			}
	});
}

</script>