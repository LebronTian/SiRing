window.onload=function(){
	var val = $("#country").val();
	var arr=val.split(",");
	var p_nation_ID = arr[3];
	$.ajax({
		url:"{{url('house/updateList/region')}}",
		data:'p_nation_ID='+p_nation_ID,
		type:'get',
		success:function (re) {
			var country = '';
			for(var i = 0;i < re.length; i++) {
				var objContry = re[i]['chinese_p_name'];
				var p_ID = re[i]['p_ID'];
				var english_p_name = re[i]['english_p_name'];
				country += '<option value="'+objContry+','+english_p_name+','+p_ID+'">'+objContry+'&nbsp;&nbsp;&nbsp;'+english_p_name+'</option>';
			}
			$("#province").html(country);
			var val = $("#province").val();
			var arr=val.split(",");
			var c_province_ID = arr[2];
			$.ajax({
				url:"{{url('house/updateList/region')}}",
				data:'c_province_ID='+c_province_ID,
				type:'get',
				success:function (re) {
					var country = '';
					for(var i = 0;i < re.length; i++) {
						var objContry = re[i]['chinese_c_name'];
						var p_ID = re[i]['c_ID'];
						var english_c_name = re[i]['english_c_name'];
						country += '<option value="'+objContry+','+english_c_name+','+p_ID+'">'+objContry+'&nbsp;&nbsp;&nbsp;'+english_c_name+'</option>';
					}
					$("#city").html(country);

				}
			});
		}
	})
}
$("select#country").change(function(){
	var val = $("#country").val();
	var arr=val.split(",");
	var p_nation_ID = arr[3];
	$.ajax({
		url:"{{url('house/updateList/region')}}",
		data:'p_nation_ID='+p_nation_ID,
		type:'get',
		success:function (re) {
			var country = '';
			for(var i = 0;i < re.length; i++) {
				var objContry = re[i]['chinese_p_name'];
				var p_ID = re[i]['p_ID'];
				var english_p_name = re[i]['english_p_name'];
				country += '<option value="'+objContry+','+english_p_name+','+p_ID+'">'+objContry+'&nbsp;&nbsp;&nbsp;'+english_p_name+'</option>';
			}
			$("#province").html(country);
			var val = $("#province").val();
			var arr=val.split(",");
			var c_province_ID = arr[2];
			$.ajax({
				url:"{{url('house/updateList/region')}}",
				data:'c_province_ID='+c_province_ID,
				type:'get',
				success:function (re) {
					var country = '';
					for(var i = 0;i < re.length; i++) {
						var objContry = re[i]['chinese_c_name'];
						var p_ID = re[i]['c_ID'];
						var english_c_name = re[i]['english_c_name'];
						country += '<option value="'+objContry+','+english_c_name+','+p_ID+'">'+objContry+'&nbsp;&nbsp;&nbsp;'+english_c_name+'</option>';
					}
					$("#city").html(country);

				}
			})
		}
	})
});
$("select#province").change(function(){
	var val = $("#province").val();
	var arr=val.split(",");
	var c_province_ID = arr[2];
	$.ajax({
		url:"{{url('house/updateList/region')}}",
		data:'c_province_ID='+c_province_ID,
		type:'get',
		success:function (re) {
			var country = '';
			for(var i = 0;i < re.length; i++) {
				var objContry = re[i]['chinese_c_name'];
				var p_ID = re[i]['c_ID'];
				var english_c_name = re[i]['english_c_name'];
				country += '<option value="'+objContry+','+english_c_name+','+p_ID+'">'+objContry+'&nbsp;&nbsp;&nbsp;'+english_c_name+'</option>';
			}
			$("#city").html(country);

		}
	})
});
