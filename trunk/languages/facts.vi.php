<?php
/*=================================================
   charset=utf-8
   Project:	phpGedView
   File:	facts.vi.php
   Author:	Lan Nguyen
   Comments:	Defines an array of GEDCOM codes and the Vietnamese name facts that they represent.
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: facts.vi.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
if (preg_match("/facts\...\.php$/", $_SERVER["PHP_SELF"])>0) {
	print "Bạn không thể vào thẳng nhu-liệu ngôn ngữ được.";
	exit;
}
// -- Define a fact array to map GEDCOM tags with their English values
$factarray["ABBR"]	= "Viết Tắt";
$factarray["ADDR"]	= "Địa Chỉ";
$factarray["ADR1"]	= "Địa Chỉ 1";
$factarray["ADR2"]	= "Địa Chỉ 2";
$factarray["ADOP"]	= "Con Nuôi";
$factarray["AFN"]	= "Hệ Số Gia Phả(AFN)";
$factarray["AGE"]	= "Tuổi";
$factarray["AGNC"]	= "Cơ Quan";
$factarray["ALIA"]	= "Tên Gọi Là";
$factarray["ANCE"]	= "Tổ Tiên";
$factarray["ANCI"]	= "Tâm Ý Của Tổ Tiên";
$factarray["ANUL"]	= "Hủy Bỏ";
$factarray["ASSO"]	= "Bạn Hữu, Cộng tác viên";
$factarray["AUTH"]	= "Tác Gỉa";
$factarray["BAPL"]	= "Bí Tích Rửa Tội Của LDS";
$factarray["BAPM"]	= "Bí Tích Rửa Tội";
$factarray["BARM"]	= "Bar Mitzvah";
$factarray["BASM"]	= "Bas Mitzvah";
$factarray["BIRT"]	= "Sinh";
$factarray["BLES"]	= "Được Ban Phép Lành";
$factarray["BLOB"]	= "Binary Data Object";
$factarray["BURI"]	= "Lể An Táng";
$factarray["CALN"]	= "Call Number";
$factarray["CAST"]	= "Giai Cấp trong Xả Hội";
$factarray["CAUS"]	= "Nguyên Do";
$factarray["CENS"]	= "Kiểm Tra";
$factarray["CHAN"]	= "Cập Nhật Hóa";
$factarray["CHAR"]	= "Bộ Chử";
$factarray["CHIL"]	= "Con";
$factarray["CHR"]	= "Lể Rửa Tội";
$factarray["CHRA"]	= "Lể Rửa Tội Cho Người Lớn";
$factarray["CITY"]	= "Thành-Phố";
$factarray["CONF"]	= "Bí-tích Thêm Sức";
$factarray["CONL"]	= "Bí-tích Thêm Sức của LDS";
$factarray["COPR"]	= "Bản Quyền";
$factarray["CORP"]	= "Công Ty";
$factarray["CREM"]	= "Hỏa Thiêu";
$factarray["CTRY"]	= "Quốc Gia";
$factarray["DATA"]	= "Tài Liệu";
$factarray["DATE"]	= "Ngày";
$factarray["DEAT"]	= "Từ Trần";
$factarray["DESC"]	= "Con cháu";
$factarray["DESI"]	= "Tâm Ý Của Các Con Cháu";
$factarray["DEST"]	= "Mục Tiêu";
$factarray["DIV"]	= "Ly Dị";
$factarray["DIVF"]	= "Ly Thân";
$factarray["DSCR"]	= "Mô Tả";
$factarray["EDUC"]	= "Trình Độ Học Vấn";
$factarray["EMIG"]	= "Di Cư";
$factarray["ENDL"]	= "LDS Endowment";
$factarray["ENGA"]	= "Lể Đính-Hôn";
$factarray["EVEN"]	= "Event";
$factarray["FAM"]	= "Gia Đình";
$factarray["FAMC"]	= "Family as a Child";
$factarray["FAMF"]	= "Sổ Gia Đình";
$factarray["FAMS"]	= "Family as a Spouse";
$factarray["FCOM"]	= "Rước Lể Lần Đầu";
$factarray["FILE"]	= "Hồ Sơ";
$factarray["FORM"]	= "Đơn Từ";
$factarray["GIVN"]	= "Tên Gọi";
$factarray["GRAD"]	= "Tốt Nghiệp";
$factarray["IDNO"]	= "Số Căn Cước";
$factarray["IMMI"]	= "Nhập Cảnh";
$factarray["LEGA"]	= "Người Thừa Kế";
$factarray["MARB"]	= "Rao Hôn-Phối";
$factarray["MARC"]	= "Giấy Giá Thú";
$factarray["MARL"]	= "Giấy Phép Hôn-Nhân";
$factarray["MARR"]	= "Hôn Lể";
$factarray["MARS"]	= "Hôn Ước";
$factarray["NAME"]	= "Tên";
$factarray["NATI"]	= "Quốc Tịch";
$factarray["NATU"]	= "Nhập Tịch";
$factarray["NCHI"]	= "Các Con";
$factarray["NICK"]	= "Biệt Danh";
$factarray["NMR"]	= "Lập Gia Đình Bao Nhiêu Lần";
$factarray["NOTE"]	= "Ghi Chú";
$factarray["NPFX"]	= "Chức Tước";
$factarray["NSFX"]	= "Suffix";
$factarray["OBJE"]	= "Multimedia Object";
$factarray["OCCU"]	= "Nghề Nghiệp";
$factarray["ORDI"]	= "Sắc-lệnh";
$factarray["ORDN"]	= "Lể Tấn Phong";
$factarray["PAGE"]	= "Citation Details";
$factarray["PEDI"]	= "Dòng Dõi";
$factarray["PLAC"]	= "Địa Điểm";
$factarray["PHON"]	= "Điện Thọai";
$factarray["POST"]	= "Bưu Cục";
$factarray["PROB"]	= "Thủ Tục Chứng Thực Di Chúc";
$factarray["PROP"]	= "Bất Động Sản";
$factarray["PUBL"]	= "Xuất Bản";
$factarray["QUAY"]	= "Phẩm Chất của Tài Liệu";
$factarray["REPO"]	= "Nơi Tàng Trữ";
$factarray["REFN"]	= "Số Tham Khảo";
$factarray["RELI"]	= "Tôn Giáo";
$factarray["RESI"]	= "Sống Lại";
$factarray["RESN"]	= "Sự Hạn Chế";
$factarray["RETI"]	= "Hưu Trí";
$factarray["RFN"]	= "Hồ-sơ số";
$factarray["RIN"]	= "Số lý-lịch";
$factarray["ROLE"]	= "Vai Trò";
$factarray["SEX"]	= "Phái";
$factarray["SLGC"]	= "LDS Child Sealing";
$factarray["SLGS"]	= "LDS Spouse Sealing";
$factarray["SOUR"]	= "Căn Nguyên";
$factarray["SPFX"]	= "Surname Prefix";
$factarray["SSN"]	= "Số An-Sinh Xả-Hội";
$factarray["STAE"]	= "Tiểu Bang";
$factarray["STAT"]	= "Địa Vị";
$factarray["SUBM"]	= "Người Đệ Trình";
$factarray["SUBN"]	= "Đệ Trình";
$factarray["SURN"]	= "Tên Họ";
$factarray["TEMP"]	= "Chùa hay Đền";
$factarray["TEXT"]	= "Nguyên Văn";
$factarray["TIME"]	= "Giờ";
$factarray["TITL"]	= "Chức Vụ";
$factarray["TYPE"]	= "Phân Lọai";
$factarray["WILL"]	= "Chúc Thư";
$factarray["_EMAIL"]	= "Địa chỉ thư điện tử";
$factarray["EMAIL"]	= "Địa chỉ thư điện tử";
$factarray["_TODO"]	= "To Do Item";
$factarray["_UID"]	= "Universal Identifier";

// These facts are specific to GEDCOM exports from Family Tree Maker
$factarray["_MDCL"]	= "Medical";
$factarray["_DEG"]	= "Bằng Cấp";
$factarray["_MILT"]	= "Nghỉa Vụ Quân-Đội";
$factarray["_SEPR"]	= "Ly Thân";
$factarray["_DETS"]	= "Cái chết của người phối ngẫu";
$factarray["CITN"]	= "Quốc Tịch";
$factarray["_FA1"]	= "Sự kiện 1";
$factarray["_FA2"]	= "Sự kiện 2";
$factarray["_FA3"]	= "Sự kiện 3";
$factarray["_FA4"]	= "Sự kiện 4";
$factarray["_FA5"]	= "Sự kiện 5";
$factarray["_FA6"]	= "Sự kiện 6";
$factarray["_FA7"]	= "Sự kiện 7";
$factarray["_FA8"]	= "Sự kiện 8";
$factarray["_FA9"]	= "Sự kiện 9";
$factarray["_FA10"]	= "Sự kiện 10";
$factarray["_FA11"]	= "Sự kiện 11";
$factarray["_FA12"]	= "Sự kiện 12";
$factarray["_FA13"]	= "Sự kiện 13";
$factarray["_MREL"]	= "Mối quan-hệ đối với Mẹ";
$factarray["_FREL"]	= "Mối quan-hệ đối với Cha";
$factarray["_MSTAT"]	= "Tình Cảnh Hôn-Phối Lúc Ban Đầu";
$factarray["_MEND"]	= "Tình Cảnh Hôn-Phối Lúc Chót";

// Other common customized facts
$factarray["_ADPF"]	= "Được Cha nhận Làm Con Nuôi";
$factarray["_ADPM"]	= "Được Mẹ Nhận Làm Con Nuôi";
$factarray["_AKAN"]	= "Tên Tự";
$factarray["_AKA"] 	= "Tên Tự";
$factarray["_BRTM"]	= "Brit mila";
$factarray["_COML"]	= "Common Law marriage";
$factarray["_EYEC"]	= "Màu Mắt";
$factarray["_FNRL"]	= "Tang Lể";
$factarray["_HAIR"]	= "Tóc Màu";
$factarray["_HEIG"]	= "Chiều Cao";
$factarray["_INTE"]	= "An Táng";
$factarray["_MARI"]	= "Marriage intention";
$factarray["_MBON"]	= "Marriage bond";
$factarray["_MEDC"]	= "Tình Trạng Sức Khỏe";
$factarray["_MILI"]	= "Quân Dịch";
$factarray["_NMR"]	= "Độc Thân";
$factarray["_NLIV"]	= "Thất Lộc";
$factarray["_NMAR"]	= "Chưa Hề Lập Gia-Dình";
$factarray["_PRMN"]	= "Permanent Number";
$factarray["_WEIG"]	= "Cân Nặng";
$factarray["_YART"]	= "Yartzeit";
$factarray["_MARNM"]	= "Tên sau khi lập gia-đình";
$factarray["_STAT"]	= "Gia Cảnh";
$factarray["COMM"]	= "Phụ Chú";

if (file_exists($PGV_BASE_DIRECTORY . "languages/facts.en.extra.php")) require $PGV_BASE_DIRECTORY . "languages/facts.en.extra.php";

?>
