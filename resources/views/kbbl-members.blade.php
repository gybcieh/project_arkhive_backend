<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KBBL Members - {{ $townName }} - {{ $barangayName }}</title>
    <style>
        @page {
            margin: 20mm;
            @top-center {
                content: "Page " counter(page) " of " counter(pages);
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1;
        }
        
        .header {
            text-align: left;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
            color: #666;
        }
        
        .members-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .members-table th,
        .members-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .members-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .members-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .member-count {
            text-align: right;
            margin-top: 10px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    @foreach($kbblGroups as $kbblIndex => $kbblData)
        @if($kbblIndex > 0)
            <div class="page-break"></div>
        @endif
        
        <div class="header">
            <h3>Town: {{ $townName }}</h3>
            <h3>Barangay: {{ $barangayName }}</h3>
            <h3>Purok: {{ $kbblData['purok'] ? $kbblData['purok']['name'] : 'N/A' }}</h3>
            <h3>KBBL: {{ $kbblData['kbbl_name'] }}</h3>
        </div>
        
        @php
            $members = $kbblData['members'];
            $totalMembers = $members->count();
            $membersPerPage = 35;
            $totalPages = max(1, ceil($totalMembers / $membersPerPage)); // Ensure at least 1 page
        @endphp
        
        @for($page = 0; $page < $totalPages; $page++)
            @if($page > 0)
                <div class="page-break"></div>
                <div class="header">
                    <h2>Town: {{ $townName }}</h2>
                    <h2>Barangay: {{ $barangayName }}</h2>
                    <h2>Purok: {{ $kbblData['purok'] ? $kbblData['purok']['name'] : 'N/A' }}</h2>
                    <h3>KBBL: {{ $kbblData['kbbl_name'] }} (Page {{ $page + 1 }} of {{ $totalPages }})</h3>
                </div>
            @endif
            
            @php
                $startIndex = $page * $membersPerPage;
                $endIndex = min(($page + 1) * $membersPerPage, $totalMembers);
                $pageMembers = $totalMembers > 0 ? $members->skip($startIndex)->take($membersPerPage) : collect([]);
            @endphp
            
            <table class="members-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">ID</th>
                        <th width="40%">KBPL</th>
                        <th width="10%">Tag</th>
                        <th width="30%">Purok</th>
                    </tr>
                </thead>
                <tbody>
                    @if($totalMembers > 0)
                        @foreach($pageMembers as $index => $member)
                            <tr>
                                <td>{{ $startIndex + $index + 1 }}</td>
                                <td>{{ $member->id }}</td>
                                <td>{{ $member->voters_name }}</td>
                                <td>
                                    @if($member->is_a)
                                        A
                                    @elseif($member->is_b)
                                        B
                                    @elseif($member->is_k)
                                        K
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $member->purok ? $member->purok->name : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; font-style: italic; color: #666;">
                                No members assigned to this KBBL
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            
            @if($page == $totalPages - 1)
                <div class="member-count">
                    Total Members: {{ $totalMembers }}
                </div>
            @endif
        @endfor
    @endforeach
</body>
</html>