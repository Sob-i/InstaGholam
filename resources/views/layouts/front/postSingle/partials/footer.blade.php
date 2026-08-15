<script>const CURRENT_USER_ID = {{ auth()->id() ?? 'null' }};</script>
<script src="{{asset('frontJs/postSingle/video.js')}}"></script>
<script src="{{asset('frontJs/comments/commentSectionShow.js')}}"></script>
<script src="{{asset('frontJs/postSingle/comment.js')}}"></script>
<script src="{{asset('frontJs/comments/deleteComment.js')}}"></script>
<script src="{{asset('frontJs/postSingle/like.js')}}"></script>
<script src="{{asset('frontJs/postSingle/save.js')}}"></script>
<script src="{{asset('frontJs/report/report.js')}}"></script>
</body>
</html>
