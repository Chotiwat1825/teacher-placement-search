@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 tracking-tight">
                ค้นหาข้อมูลการบรรจุครู
            </h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">
                ระบบสืบค้นข้อมูลการประกาศเรียกบรรจุและแต่งตั้งครูผู้ช่วย
            </p>
        </header>

        <!-- Search Form Card -->
        <div class="bg-white shadow-xl rounded-lg p-6 md:p-8 mb-10 max-w-4xl mx-auto">
            <form method="GET" action="{{ route('search.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-6">
                    <div>
                        <label for="educational_area_id"
                            class="block text-sm font-medium text-gray-700 mb-1">เขตพื้นที่การศึกษา:</label>
                        <select name="educational_area_id" id="educational_area_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                            <option value="">-- ทั้งหมด --</option>
                            @foreach ($educationalAreas as $area)
                                <option value="{{ $area->id }}"
                                    {{ old('educational_area_id', $request->educational_area_id) == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">ปีการศึกษา
                            (พ.ศ.):</label>
                        <select name="academic_year" id="academic_year"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                            <option value="">-- ทั้งหมด --</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year }}"
                                    {{ old('academic_year', $request->academic_year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="subject_group_id" class="block text-sm font-medium text-gray-700 mb-1">กลุ่มวิชาเอก
                            (ค้นหา):</label>
                        <select name="subject_group_id" id="subject_group_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                            <option value="">-- ทั้งหมด --</option>
                            @foreach ($subjectGroupsForFilter as $group)
                                <option value="{{ $group->id }}"
                                    {{ old('subject_group_id', $request->subject_group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('search.index') }}"
                        class="text-sm text-gray-600 hover:text-indigo-600 hover:underline transition-colors">
                        ล้างค่าการค้นหา
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        ค้นหาข้อมูล
                    </button>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        @if ($placements && $placements->count() > 0)
            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>s
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    ปี พ.ศ.</th>
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    เขตพื้นที่ฯ</th>
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    กลุ่มวิชาเอก</th>
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    รอบที่</th>
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    วันที่ประกาศ</th>
                                <th scope="col"
                                    class="px-4 sm:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($placements as $placement)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $placement->academic_year }}</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">
                                        {{ $placement->educationalArea->name }}</td>
                                    <td class="px-4 sm:px-6 py-4 text-sm text-gray-700">
                                        @if ($placement->subjectGroups->isNotEmpty())
                                            {{ $placement->subjectGroups->pluck('name')->implode(', ') }}
                                        @else
                                            <span class="text-gray-400 italic">ไม่ระบุ</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $placement->round_number }}</td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $placement->announcement_date ? $placement->announcement_date->format('j F Y') : '-' }}
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <a href="{{ route('placement.details', $placement->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination -->
            @if ($placements->hasPages())
                <div class="mt-8">
                    {{ $placements->links() }} {{-- Tailwind CSS pagination views are default --}}
                </div>
            @endif
        @elseif(request()->hasAny(['educational_area_id', 'academic_year', 'subject_group_id']))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-md shadow-md max-w-2xl mx-auto">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1.75-3.75a.75.75 0 00-1.5 0v3.5a.75.75 0 001.5 0v-3.5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-yellow-800">ไม่พบข้อมูลการบรรจุ</h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            <p>ไม่พบข้อมูลตามเงื่อนไขที่ท่านระบุ กรุณาลองตรวจสอบหรือค้นหาใหม่อีกครั้ง</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-md shadow-md max-w-2xl mx-auto">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-blue-800">เริ่มต้นการค้นหา</h3>
                        <div class="mt-1 text-sm text-blue-700">
                            <p>กรุณาเลือกเงื่อนไขจากแบบฟอร์มด้านบน เพื่อค้นหาข้อมูลการบรรจุครูที่คุณต้องการ</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
