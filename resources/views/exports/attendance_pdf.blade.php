<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo điểm danh {{ $lopHocPhan->ma_lhp }}</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 10px; }
        h1 { margin: 0 0 5px; font-size: 18px; }
        p { margin: 3px 0 14px; color: #475569; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e2e8f0; font-weight: bold; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 6px; text-align: left; }
        td:nth-child(n+3), th:nth-child(n+3) { text-align: center; }
        .footer { margin-top: 18px; color: #64748b; font-size: 9px; }
    </style>
</head>
<body>
    <h1>Báo cáo thống kê điểm danh</h1>
    <p>{{ $lopHocPhan->ma_lhp }} - {{ $lopHocPhan->monHoc->ten_mon }} | Xuất ngày {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Tài liệu được xuất từ Hệ thống Điểm Danh Trực Tuyến.</p>
</body>
</html>
