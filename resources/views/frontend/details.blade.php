@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg p-6 md:p-10 my-6 max-w-4xl mx-auto">
            <!-- Header Section -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b border-gray-200">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">
                        รายละเอียดการประกาศบรรจุครู
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        @if ($placementRecord->subjectGroups->isNotEmpty())
                            {{ $placementRecord->subjectGroups->first()->name }}
                            @if ($placementRecord->subjectGroups->count() > 1)
                                และสาขาอื่นๆ
                            @endif
                        @else
                            <span class="italic">กลุ่มวิชาเอกไม่ระบุ</span>
                        @endif
                        - {{ $placementRecord->educationalArea->name }}
                    </p>
                </div>
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('search.index') }}"
                    class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    กลับไปหน้าค้นหา
                </a>
            </div>

            <!-- Main Details Grid -->
            <div class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-indigo-700 mb-3">ข้อมูลทั่วไปของการประกาศ</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">ปีการบรรจุ (พ.ศ.)</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $placementRecord->academic_year }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">วันที่ประกาศ</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $placementRecord->announcement_date ? $placementRecord->announcement_date->format('j F Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">รอบการเรียกบรรจุ</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $placementRecord->round_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">เขตพื้นที่การศึกษา</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $placementRecord->educationalArea->name }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">กลุ่มวิชาเอกที่ประกาศ:</p>
                        @if ($placementRecord->subjectGroups->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($placementRecord->subjectGroups as $subject)
                                    <span
                                        class="inline-block bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1 rounded-full">
                                        {{ $subject->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-1 text-lg font-semibold text-gray-900 italic">ไม่ระบุ</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attachments and Source Link Section -->
            <div class="space-y-6 mb-8 pb-8 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-indigo-700 mb-3">ที่มาของข้อมูล / เอกสารแนบ</h2>
                @if ($placementRecord->source_link)
                    <div class="p-4 bg-gray-50 rounded-md border border-gray-200">
                        <p class="text-sm font-medium text-gray-600 mb-1">Link ที่มาของข้อมูล:</p>
                        <a href="{{ $placementRecord->source_link }}" target="_blank" rel="noopener noreferrer"
                            class="text-indigo-600 hover:text-indigo-800 hover:underline break-all transition-colors duration-150 text-sm flex items-center">
                            {{ $placementRecord->source_link }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-1.5 shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                @endif

                @if ($placementRecord->attachments && $placementRecord->attachments->count() > 0)
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-2">ไฟล์แนบ/รูปภาพ:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($placementRecord->attachments as $attachment)
                                <a href="{{ route('attachments.view', $attachment->id) }}" target="_blank"
                                    class="block border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-all duration-150 group text-center transform hover:-translate-y-1">
                                    @if ($attachment->type === 'image' && Str::startsWith($attachment->mime_type, 'image/'))
                                        <div class="w-full h-32 sm:h-36 bg-gray-100 rounded-md overflow-hidden mb-2">
                                            <img src="{{ route('attachments.view', $attachment->id) }}?preview=true"
                                                alt="{{ $attachment->original_filename }}"
                                                class="w-full h-full object-contain group-hover:opacity-90 transition-opacity">
                                        </div>
                                    @else
                                        <div
                                            class="flex flex-col items-center justify-center h-32 sm:h-36 bg-gray-50 rounded-md mb-2 p-2 border border-dashed border-gray-300">
                                            @if (Str::contains($attachment->mime_type, 'pdf'))
                                                <svg class="w-10 h-10 text-red-500 mb-1" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M3 17a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H3zm5.5-7.5a.5.5 0 00-1 0V12a.5.5 0 00.5.5h1.5a.5.5 0 000-1H9v-2zM13 9a1 1 0 100-2 1 1 0 000 2zm-1 1.5a.5.5 0 00-.5-.5h-2a.5.5 0 000 1h1.5V12a.5.5 0 001 0v-1.5zM6.25 9a.75.75 0 100-1.5.75.75 0 000 1.5zM5 11.5A.5.5 0 015.5 11h.01a.5.5 0 010 1H5.5a.5.5 0 01-.5-.5z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @elseif(Str::contains($attachment->mime_type, ['word', 'document']))
                                                <svg class="w-10 h-10 text-blue-500 mb-1" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M3 3a2 2 0 00-2 2v10a2 2 0 002 2h3.586a1 1 0 00.707-.293l6-6A1 1 0 0013.586 8H10a2 2 0 01-2-2V3a2 2 0 00-2-2H3zm14-1a2 2 0 00-2-2H7.586a1 1 0 00-.707.293l-2.586 2.586A1 1 0 004 6.586V15a2 2 0 002 2h8a2 2 0 002-2V3a1 1 0 00-1-1z" />
                                                </svg>
                                            @else
                                                <svg class="w-10 h-10 text-gray-400 mb-1" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            @endif
                                            <p class="text-xs text-gray-500">ประเภท:
                                                {{ strtoupper(pathinfo($attachment->original_filename, PATHINFO_EXTENSION)) }}
                                            </p>
                                        </div>
                                    @endif
                                    <p class="text-xs sm:text-sm text-indigo-700 font-medium group-hover:underline truncate leading-tight"
                                        title="{{ $attachment->original_filename }}">
                                        {{ Str::limit($attachment->original_filename, 25) }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!$placementRecord->source_link && (!$placementRecord->attachments || $placementRecord->attachments->count() == 0))
                    <div class="text-center py-6 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-700">ไม่มีข้อมูลที่มาหรือไฟล์แนบ</h3>
                        <p class="mt-1 text-xs">ไม่พบการระบุแหล่งที่มาของข้อมูล หรือเอกสารแนบสำหรับรายการนี้</p>
                    </div>
                @endif
            </div>

            <!-- Related Rounds Section -->
            @if ($relatedRounds && $relatedRounds->count() > 0)
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-3">ข้อมูลการประกาศรอบอื่นที่เกี่ยวข้อง</h2>
                    @foreach ($relatedRounds as $round)
                        <a href="{{ route('placement.details', $round->id) }}"
                            class="block p-4 bg-gray-50 hover:bg-indigo-50 rounded-lg shadow-sm transition-all duration-150 border border-gray-200 hover:border-indigo-300 transform hover:scale-[1.01]">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-md font-semibold text-indigo-700">
                                        รอบการเรียกบรรจุ: {{ $round->round_number }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        ประกาศวันที่:
                                        {{ $round->announcement_date ? $round->announcement_date->format('j F Y') : 'N/A' }}
                                    </p>
                                    @if ($round->subjectGroups->isNotEmpty())
                                        <p class="text-xs text-gray-500 mt-1">
                                            กลุ่มวิชาเอก: {{ $round->subjectGroups->pluck('name')->implode(', ') }}
                                        </p>
                                    @endif
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-indigo-400 group-hover:text-indigo-600 transition-colors"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
