<h3 class="page-product-heading" id="mymodcomments-content-tab"{if
 isset($new_comment_posted)} data-scroll="true"{/if}>{l s='Product Comments' mod='mymodcomments'}</h3>
<div class="rte">
 {foreach from=$comments item=comment}
 <p>
 <strong>Comment #{$comment.id_mymod_comment}:</strong>
 {$comment.comment}<br>
 <strong>Grade:</strong> {$comment.grade}/5<br>
 </p><br>
 {/foreach}
</div> 
<div class="rte">
 <form action="" method="POST" id="comment-form">
 <div class="form-group">
 <label for="grade">Grade:</label>
 <div class="row">
 <div class="col-xs-4">
 <input id="grade" name="grade" value="0" type="number"
 class="rating" min="0" max="5" step="1" data-size="sm" >
 </select>
 </div>
 </div>
 </div>
 <div class="form-group">
 <label for="comment">Comment:</label>
 <textarea name="comment" id="comment" class="form-
 control"></textarea>
 </div>
 <div class="submit">
 <button type="submit" name="mymod_pc_submit_comment"
 class="button btn btn-default button-medium">
 <span>Send<i class="icon-chevron-right right">
 </i></span></button>
 </div>
 </form>
</div>