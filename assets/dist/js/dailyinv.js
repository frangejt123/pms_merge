$(document).ready(function () {
	/* populate measurement list */

	var rawmat_type = [];

	$("#li-rawmat_menu").addClass("menu-open");
	$("#li-rawmat_menu ul").css({ display: "block" });
	$("#li-rawmat_dailyinv").addClass("active");

	$("input#period_date, input#period_date_detail").datepicker();

	$(".daily_inv_data").slimScroll();

	$("table#daily_inv_rawmat").on("focus", ".daily_inv_qty", function () {
		$(".daily_inv_qty").parent().parent().parent().removeClass("active");
		$(this).parent().parent().parent().addClass("active");
	});

	$.ajax({
		method: "POST",
		url: baseurl + "/dailyinventory/getAll",
		success: function (res) {
			var res = JSON.parse(res);
			var tr = "";
			var statuslbl = ["Pending", "Approved"];
			var statuscls = ["text-success", "text-primary"];

			$.each(res, function (ind, row) {
				tr +=
					'<tr id="' +
					row["id"] +
					'"><td class="tr_period_date">' +
					row["period"] +
					"</td><td>" +
					row["branch"] +
					"</td><td>" +
					row["userfname"] +
					" " +
					row["userlname"] +
					"</td><td class='di_status " +
					statuscls[row["status"]] +
					"'>" +
					statuslbl[row["status"]] +
					"</td></tr>";
			});

			$("table#dailyinv_table tbody").html(tr);
			$("table#dailyinv_table").DataTable({
				paging: true,
				lengthChange: false,
				searching: true,
				ordering: false,
				info: false,
				autoWidth: false,
				pageLength: 20,
			});
		},
	});

	var rawmat = [];

	$.ajax({
		method: "POST",
		url: baseurl + "/rawmaterial/getAll",
		success: function (res) {
			var res = JSON.parse(res);
			rawmat = res;
			var tr = "";
			var tabindex = 1;

			$.each(rawmat, function (ind, row) {
				tr +=
					'<tr id="' +
					row["id"] +
					'">' +
					"<td>" +
					row["itemcode"] +
					"</td>" +
					"<td>" +
					row["description"] +
					"</td>" +
					'<td style="width: 20%; height: 20px;">' +
					'<div class="input-group">' +
					'<input type="text" class="form-control daily_inv_qty" value="" tabindex="' +
					tabindex +
					'">' +
					"</td> " +
					"</tr>";
				tabindex++;
			});

			$(
				"table#daily_inv_rawmat tbody, table#daily_inv_detail_rawmat tbody"
			).html(tr);
		},
	});

	$("button#new_daily_inv_btn").on("click", function () {
		$("div#new_daily_inv_modal").modal("show");
	});

	$("div#daily_inv_detail_modal").on("shown.bs.modal", function () {
		var id = $("div#daily_inv_detail_modal").data("id");
		$("div.progress_mask").show();
		populaterow(id);
	});

	$("#newDI_submitBtn").on("click", function () {
		var itemlist = $("table#daily_inv_rawmat").find("tbody tr");
		var perioddate = $("input#period_date").val();
		var paramdata = {};
		paramdata["perioddate"] = perioddate;
		paramdata["detail"] = [];

		if (perioddate == "") {
			$.bootstrapGrowl(
				"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in period date.",
				{
					type: "danger",
					width: 300,
				}
			);
			return;
		}

		$.each(itemlist, function (ind, row) {
			var rawmatid = $(row).attr("id");
			var qty = $(row).find("input.daily_inv_qty").val();
			var data = {};
			data["rawmat_id"] = rawmatid;
			data["qty"] = qty;
			paramdata["detail"].push(data);
		});

		$.ajax({
			method: "POST",
			data: paramdata,
			url: baseurl + "/dailyinventory/saveDailyinventory",
			success: function (res) {
				var res = JSON.parse(res);
				if (res["success"]) {
					$("div#new_daily_inv_modal").modal("hide");
					$.bootstrapGrowl(
						"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!",
						{
							type: "success",
							width: 300,
						}
					);

					var tr =
						'<tr id="' +
						res["id"] +
						'"><td>' +
						perioddate +
						"</td><td>" +
						res[0]["branch_name"] +
						"</td><td>" +
						res[0]["firstname"] +
						" " +
						res[0]["lastname"] +
						"</td><td class='di_status text-success'>Pending</td></tr>";

					$("table#dailyinv_table tbody").prepend(tr);
				}
			},
		});
	});

	$("#updateDI_submitBtn").on("click", function () {
		var itemlist = $("table#daily_inv_detail_rawmat").find("tbody tr");
		var periodid = $("div#daily_inv_detail_modal").data("id");
		var perioddate = $("input#period_date_detail").val();

		var status = $("#dailyinv_table tbody tr#" + periodid)
			.find("td.di_status")
			.html();

		if (status == "Approved") {
			return;
		}

		var paramdata = {};
		paramdata["daily_inv"] = { id: periodid, period: perioddate };
		paramdata["detail"] = [];
		$.each(itemlist, function (ind, row) {
			var mode = "update";
			var rawmatid = $(row).attr("detail-id");
			if (rawmatid == undefined) {
				rawmatid = $(row).attr("id");
				mode = "new";
			}
			var qty = $(row).find("input.daily_inv_qty").val();
			var data = {};
			data["id"] = rawmatid;
			data["qty"] = qty;
			data["mode"] = mode;
			data["period_id"] = periodid;
			paramdata["detail"].push(data);
		});

		$.ajax({
			method: "POST",
			data: paramdata,
			url: baseurl + "/dailyinventory/updateDailyinventory",
			success: function (res) {
				var res = JSON.parse(res);
				if (res["success"]) {
					$("div#daily_inv_detail_modal").modal("hide");
					$.bootstrapGrowl(
						"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!",
						{
							type: "success",
							width: 300,
						}
					);
				}
			},
		});
	});

	$("#approve_di").on("click", function () {
		$("#approve_confirm_modal").modal("show");
	});

	$("#confirm_approve_btn").on("click", function () {
		var periodid = $("div#daily_inv_detail_modal").data("id");
		var status = $("#dailyinv_table tbody tr#" + periodid)
			.find("td.di_status")
			.html();

		if (status == "Approved") {
			return;
		}
		$.ajax({
			method: "POST",
			data: { id: periodid, status: 1 },
			url: baseurl + "/dailyinventory/approve",
			success: function (res) {
				var res = JSON.parse(res);
				if (res["success"]) {
					$("#dailyinv_table tbody tr#" + periodid)
						.find("td.di_status")
						.html("Approved")
						.addClass("text-primary")
						.removeClass("text-success");

					$("#approve_confirm_modal").modal("hide");
					$("div#daily_inv_detail_modal").modal("hide");

					$.bootstrapGrowl(
						"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!",
						{
							type: "success",
							width: 300,
						}
					);
				}
			},
		});
	});

	/* on row click */
	$("table#dailyinv_table tbody").on("click", "tr", function () {
		var id = $(this).attr("id");
		var period_date = $(this).find("td.tr_period_date").html();
		var status = $(this).find("td.di_status").html();
		$("button#approve_di").attr("disabled", false);
		$("button#updateDI_submitBtn").attr("disabled", false);
		$("button#delete_di").attr("disabled", false);

		if (status == "Approved") {
			$("button#approve_di").attr("disabled", true);
			$("button#updateDI_submitBtn").attr("disabled", true);
			$("button#delete_di").attr("disabled", true);
		}
		$("input#period_date_detail").val(period_date);
		$("div#daily_inv_detail_modal").data("id", id).modal("show");
	});

	$("table#daily_inv_rawmat, table#daily_inv_detail_rawmat")
		.on("keypress", "input.daily_inv_qty", function (eInner) {
			if (eInner.keyCode == 13) {
				//if its a enter key
				var tabindex = $(this).attr("tabindex");
				tabindex++; //increment tabindex
				//after increment of tabindex ,make the next element focus
				$("[tabindex=" + tabindex + "]").focus();

				return false; // to cancel out Onenter page postback in asp.net
			}
		})
		.on("blur", "input.daily_inv_qty", function () {
			var str = $(this).val();
			str = str.replace(/[^0-9\.]+/g, "");
			$(this).val(str);
		});

	function ucwords(str) {
		return str.replace(/(^\w{1})|(\s+\w{1})/g, (letter) =>
			letter.toUpperCase()
		);
	}

	function populaterow(id) {
		$.ajax({
			method: "POST",
			data: { period_id: id },
			url: baseurl + "/dailyinventory/detail",
			success: function (res) {
				var res = JSON.parse(res);
				$.each(res, function (ind, row) {
					$("div#daily_inv_detail_modal table#daily_inv_detail_rawmat")
						.find("tr#" + row["rawmat_id"])
						.attr("detail-id", row["id"]);
					$("div#daily_inv_detail_modal table#daily_inv_detail_rawmat")
						.find("tr#" + row["rawmat_id"] + " input")
						.val(row["qty"]);
				});
				$("div.progress_mask").hide();
			},
		});
	}
});
