$(document).ready(function () {
	/* populate measurement list */

	var rawmat_type = [];

	$("#li-rawmat_menu").addClass("menu-open");
	$("#li-rawmat_menu ul").css({ display: "block" });
	$("#li-rawmat_master").addClass("active");

	$.ajax({
		method: "POST",
		url: baseurl + "/rawmaterial/getAll",
		success: function (res) {
			var res = JSON.parse(res);
			var tr = "";

			$.each(res, function (ind, row) {
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
					'<td id="' +
					row["type_id"] +
					'">' +
					row["type_description"] +
					"</td>" +
					'<td id="' +
					row["uom"] +
					'">' +
					row["uom_description"] +
					"</td>" +
					"</tr>";
			});

			$("table#rawmaterialtable tbody").html(tr);
			$("table#rawmaterialtable").DataTable({
				paging: true,
				lengthChange: false,
				searching: true,
				ordering: true,
				info: false,
				autoWidth: false,
				pageLength: 20,
			});
		},
	});

	$("button#new_rm_btn").on("click", function () {
		$("div#new_rm_modal").modal("show");
		// var inputs = $("form#newProductForm").find("input");
		// $.each(inputs, function(ind, row){
		// 	$(this).removeClass("emptyField");
		// 	$(this).val("");
		// });
		// $("select#product_uom").removeClass("emptyField");
	});

	$("div#new_rm_modal").on("shown.bs.modal", function () {
		populateSelect2(null, null);
	});

	$("#newRM_submitBtn").on("click", function () {
		var description = $("input#description").val();
		var type = $("#rm_type").val();
		var uom = $("#rm_uom").val();
		var itemcode = $("#rm_itemcode").val();

		var type_description = $("select#rm_type option:selected").html();
		var uom_description = $("select#rm_uom option:selected").html();

		var data = {
			itemcode: itemcode,
			description: description,
			type_id: type,
			uom: uom,
		};

		var inputs = $("form#newRMForm").find("input");
		var empty = 0;
		$.each(inputs, function (ind, row) {
			$(this).removeClass("emptyField");
			if ($(this).val() == "") {
				$(this).addClass("emptyField");
				empty++;
			}
		});

		if (empty > 0) {
			$.bootstrapGrowl(
				"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.",
				{
					type: "danger",
					width: 300,
				}
			);
			return;
		}

		$.ajax({
			method: "POST",
			data: { itemcode },
			url: baseurl + "/rawmaterial/checkcode",
			success: function (res) {
				if (res > 0) {
					alert("Item code already exist.");
				} else {
					$.ajax({
						method: "POST",
						data: data,
						url: baseurl + "/rawmaterial/insert",
						success: function (res) {
							var res = JSON.parse(res);
							if (res["success"]) {
								var tr =
									'<tr id="' +
									res["id"] +
									'">' +
									"<td>" +
									itemcode +
									"</td>" +
									"<td>" +
									description +
									"</td>" +
									'<td id="' +
									type +
									'">' +
									type_description +
									"</td>" +
									'<td id="' +
									uom +
									'">' +
									uom_description +
									"</td>" +
									"</tr>";

								$("table#rawmaterialtable tbody").prepend(tr);
								$("div#new_rm_modal").modal("hide");

								$("table#rawmaterialtable")
									.DataTable()
									.row.add([
										itemcode,
										description,
										type_description,
										uom_description,
									])
									.draw(false);

								$.bootstrapGrowl(
									"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!",
									{
										type: "success",
										allow_dismiss: false,
										width: 300,
									}
								);
							}
						},
					});
				}
			},
		});
	});

	/* on row click */
	$("table#rawmaterialtable tbody").on("click", "tr", function () {
		if (!$(this).parent().hasClass("edit")) return;
		var id = $(this).attr("id");

		$("div#rm_detail_modal").data("id", id);
		$("div#rm_detail_modal").modal("show");
	});

	$("div#rm_detail_modal").on("shown.bs.modal", function () {
		var id = $("div#rm_detail_modal").data("id");

		var tds = $("table#rawmaterialtable tbody tr#" + id).find("td");

		var itemcode = $(tds[0]).html();
		var description = $(tds[1]).html();
		var typeval = $(tds[2]).attr("id");
		var uomval = $(tds[3]).attr("id");

		$("input#update_rm_itemcode").val(itemcode);
		$("input#update_description").val(description);
		populateSelect2(typeval, uomval);
	});

	$("#updateRM_submitBtn").on("click", function () {
		var id = $("div#rm_detail_modal").data("id");
		var description = $("input#update_description").val();
		var type = $("#update_rm_type").val();
		var uom = $("#update_rm_uom").val();
		var itemcode = $("#update_rm_itemcode").val();

		var type_description = $("select#update_rm_type option:selected").html();
		var uom_description = $("select#update_rm_uom option:selected").html();

		var d = {
			id: id,
			description: description,
			type_id: type,
			uom: uom,
		};

		var inputs = $("form#detailRMForm").find("input");

		var empty = 0;
		$.each(inputs, function (ind, row) {
			$(this).removeClass("emptyField");
			if ($(this).val() == "") {
				$(this).addClass("emptyField");
				empty++;
			}
		});

		if (empty > 0) {
			$.bootstrapGrowl(
				"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.",
				{
					type: "danger",
					width: 300,
				}
			);
			return;
		}

		$.ajax({
			method: "POST",
			data: { id, itemcode },
			url: baseurl + "/rawmaterial/checkcode",
			success: function (res) {
				if (res > 0) {
					alert("Item code already exist.");
				} else {
					$.ajax({
						method: "POST",
						data: d,
						url: baseurl + "/rawmaterial/update",
						success: function (res) {
							var res = JSON.parse(res);
							if (res["success"]) {
								var td =
									"<td>" +
									itemcode +
									"</td>" +
									"<td>" +
									description +
									"</td>" +
									'<td id="' +
									type +
									'">' +
									type_description +
									"</td>" +
									'<td id="' +
									uom +
									'">' +
									uom_description +
									"</td>";

								$("table#rawmaterialtable tbody tr#" + id).html(td);
								$("div#rm_detail_modal").modal("hide");

								$.bootstrapGrowl(
									"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!",
									{
										type: "success",
										width: 300,
									}
								);
							}
						},
					});
				}
			},
		});
	});

	/*delete record*/
	$("button#delete_rm").on("click", function () {
		$("div#confirm_modal").modal("show");
	});

	$("a#confirm_delete_rm_btn").on("click", function () {
		var id = $("div#rm_detail_modal").data("id");

		var data = {
			id: id,
		};

		$.ajax({
			url: baseurl + "/rawmaterial/delete",
			method: "POST",
			data: data,
			success: function (data) {
				var data = JSON.parse(data);
				if (data["success"]) {
					$.bootstrapGrowl(
						"&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.",
						{
							type: "success",
							width: 300,
						}
					);

					$("div#rm_detail_modal").modal("hide");
					$("table#rawmaterialtable")
						.find("tr#" + id)
						.remove();
				}
			},
		});

		$("div#rm_detail_modal").on("hide.bs.modal", function () {
			$("div#confirm_modal").modal("hide");
			$("html, body").css({
				overflow: "hidden",
				height: "100%",
			});
		});
	});

	var current_rawmat_type;

	$("button#view_type_btn").on("click", function () {
		var tr = "";
		$.each(rawmat_type, function (ind, row) {
			if (row) {
				tr +=
					'<tr id="' +
					row["id"] +
					'" class="rawmattype_tr">' +
					"<td  class='rawmattype_desc'>" +
					row["description"] +
					"</td>" +
					"</tr>";
			}
		});

		current_rawmat_type = $(".select2#update_rm_type").val();
		$("table#raw_material_type_table tbody").html(tr);
		$("input#type_desc").val("");
		$("#raw_mat_modal").modal("show");
	});

	$.ajax({
		method: "POST",
		url: baseurl + "/rawmaterial/getType",
		success: function (res) {
			var res = JSON.parse(res);
			// $.each(res, function (ind, row) {
			// 	rawmat_type[" " + row["id"]] = row;
			// });
			rawmat_type = res;
		},
	});

	$("#save_rawmat_type").on("click", function () {
		var tr = $("#raw_material_type_table").find("tr.haschanges");
		var rowdata = {};
		$.each(tr, function (ind, row) {
			var id = $(row).attr("id");
			var value = $(row).find("td").html();
			var method = $(row).attr("data-method");

			rowdata[ind] = { id, value, method };
		});

		$.ajax({
			method: "POST",
			data: rowdata,
			url: baseurl + "/rawmaterial/savetypechanges",
			success: function (res) {
				var res = JSON.parse(res);
				if (res["success"]) {
					var optcount = rawmat_type.length;

					$.each(res["data"], function (ind, row) {
						var dataIndex = rawmat_type.findIndex(
							(item) => item.id === row["id"]
						);
						if (row["method"] == "delete") {
							rawmat_type.splice(dataIndex, 1);
						} else if (row["method"] == "edit") {
							rawmat_type[dataIndex]["text"] = row["description"];
							rawmat_type[dataIndex]["description"] = row["description"];
						} else {
							var dataIndex = rawmat_type.findIndex(
								(item) => item.id === row["id"]
							);
							rawmat_type[optcount] = {};
							rawmat_type[optcount]["id"] = row["id"].toString();
							rawmat_type[optcount]["text"] = row["description"];
							rawmat_type[optcount]["description"] = row["description"];
							rawmat_type[optcount]["color"] = null;
						}
					});

					$(".select2#rm_type, .select2#update_rm_type")
						.select2("destroy")
						.empty()
						.select2({
							data: rawmat_type,
						})
						.val(current_rawmat_type)
						.trigger("change");

					$.bootstrapGrowl(
						"&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!",
						{
							type: "success",
							allow_dismiss: false,
							width: 300,
						}
					);

					$("#raw_mat_modal").modal("hide");
				}
			},
		});
	});

	$("#add_rawmat_type").on("click", function () {
		var type_desc = $("input#type_desc").val();

		if (type_desc == "") {
			alert("Please fill in required fields.");
			return;
		}

		var typelist = $("table#raw_material_type_table").find("tr.rawmattype_tr");

		var exist = 0;
		$.each(typelist, function (ind, row) {
			var ucrow = $(row).find("td.rawmattype_desc").html().toUpperCase();
			if (ucrow == type_desc.toUpperCase()) {
				exist++;
			}
		});

		if (exist > 0) {
			alert("Data already exist");
			return;
		} else {
			var tr =
				"<tr class='rawmattype_tr text-success new haschanges' data-method='new'>" +
				"<td class='rawmattype_desc'>" +
				ucwords(type_desc) +
				"</td>" +
				"</tr>";

			$("table#raw_material_type_table tbody").prepend(tr);
		}

		// $("#compositionproduct").val("").trigger("change");
		// $("#compositionqty").val("");
		// $("div#opt-btn").hide();
	});

	$("#raw_material_type_table").on("click", ".rawmattype_tr", function () {
		$("#raw_material_type_table .rawmattype_tr").removeClass("info selected");
		$(this).addClass("info selected");
		var desc = $(this).find(".rawmattype_desc").html();

		$("#opt-btn").show();
		$("#opt-btn .hidden-btn").hide();

		$("#addbtn_row").hide();
		$("#update_rawmat_type").show();
		$("#cancel_rawmat_type").show();
		$("#delete_rawmattype_selection").show();
		$("input#type_desc").val(desc);

		if ($(this).hasClass("haschanges")) {
			$("#rawmat_undo_changes").show();

			if ($(this).hasClass("row-deleted")) {
				$("#update_rawmat_type").hide();
				$("#delete_rawmattype_selection").hide();
			} else if ($(this).hasClass("new")) {
				$("#delete_rawmattype_selection").hide();
			}
		}
	});

	$("#rawmat_undo_changes").on("click", function () {
		var selected = $("#raw_material_type_table .rawmattype_tr.selected");
		var selected_id = selected.attr("id");
		$(".updatebtn_row").hide();
		$("#rawmat_undo_changes").hide();
		$("#opt-btn").hide();
		$("#addbtn_row").show();
		$("input#type_desc").val("");
		if (selected.hasClass("new")) {
			selected.remove();
		} else {
			selected.find("td").html(rawmat_type[selected_id]["description"]);
		}
		selected.removeClass(
			"haschanges text-info row-deleted text-danger info selected"
		);
		selected.removeAttr("data-method");
	});

	$("#cancel_rawmat_type").on("click", function () {
		$("#raw_material_type_table .rawmattype_tr").removeClass("info");
		$("#opt-btn").hide();
		$(".updatebtn_row").hide();
		$("#addbtn_row").show();
		$("input#type_desc").val("");
	});

	$("#delete_rawmattype_selection").on("click", function () {
		var selected = $("#raw_material_type_table").find(
			".rawmattype_tr.selected"
		);

		selected
			.addClass("haschanges text-danger row-deleted")
			.attr("data-method", "delete")
			.find("td")
			.html($("input#type_desc").val());

		$("#addbtn_row").show();
		$(".updatebtn_row").hide();
		$("#opt-btn").hide();
		selected.removeClass("info selected");
	});

	$("#update_rawmat_type").on("click", function () {
		var selected = $("#raw_material_type_table").find(
			".rawmattype_tr.selected"
		);

		$(selected)
			.addClass("haschanges text-info")
			.attr("data-method", "edit")
			.find("td")
			.html($("input#type_desc").val());

		$("#raw_material_type_table .rawmattype_tr").removeClass("info");
		$("#opt-btn").hide();
		$("#addbtn_row").show();
		$(".updatebtn_row").hide();
		$("input#type_desc").val("");
	});

	$("#print_raw_mat").on("click", function () {
		// $.ajax({
		// 	method: "POST",
		// 	url: baseurl + "/report/rawamteriallist",
		// 	success: function (res) {},
		// });
		window.open(baseurl + "/report/rawamteriallist");
	});

	function populateSelect2(type, uom) {
		// var dateType = [
		// 	{ id: "", text: "" },
		// 	{ id: "0", text: "Raw Material" },
		// 	{ id: "1", text: "Premix & Sauce" },
		// 	{ id: "2", text: "Drinks" },
		// ];
		// console.log(rawmat_type);
		// console.log(dateType);

		$(".select2#rm_type, .select2#update_rm_type")
			.select2({
				placeholder: "Select Type",
				data: rawmat_type,
			})
			.val(type)
			.trigger("change");

		$.ajax({
			method: "POST",
			url: baseurl + "/uom/getAll",
			success: function (res) {
				var res = JSON.parse(res);
				var dataUom = [{ id: "", text: "" }];

				$.each(res, function (i, r) {
					dataUom.push({ id: r["id"], text: r["description"] });
				});

				$(".select2#rm_uom, .select2#update_rm_uom")
					.select2({
						placeholder: "Select Unit of Measurement",
						data: dataUom,
					})
					.val(uom)
					.trigger("change");
			},
		});
	}

	function ucwords(str) {
		return str.replace(/(^\w{1})|(\s+\w{1})/g, (letter) =>
			letter.toUpperCase()
		);
	}
});
