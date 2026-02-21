<div class="overflow-x-auto">
  <table class="w-full text-sm text-left border border-gray-200 dark:border-gray-700 rounded-lg">
    <thead class="bg-gray-50 dark:bg-gray-800">
      <tr>
        <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">No</th>
        <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">Nama Siswa</th>
        <th class="px-4 py-2 font-semibold text-gray-700 dark:text-gray-300">NISN</th>
        <th class="px-4 py-2 font-semibold text-center text-gray-700 dark:text-gray-300">Total</th>
        <th class="px-4 py-2 font-semibold text-center text-gray-700 dark:text-gray-300">Verified</th>
        <th class="px-4 py-2 font-semibold text-center text-gray-700 dark:text-gray-300">Pending</th>
        <th class="px-4 py-2 font-semibold text-center text-gray-700 dark:text-gray-300">Rejected</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
      @forelse ($getState() as $index => $siswa)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
          <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
          <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $siswa['name'] }}</td>
          <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $siswa['nisn'] }}</td>
          <td class="px-4 py-2 text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400">
              {{ $siswa['total'] }}
            </span>
          </td>
          <td class="px-4 py-2 text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">
              {{ $siswa['verified'] }}
            </span>
          </td>
          <td class="px-4 py-2 text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $siswa['pending'] > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
              {{ $siswa['pending'] }}
            </span>
          </td>
          <td class="px-4 py-2 text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $siswa['rejected'] > 0 ? 'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
              {{ $siswa['rejected'] }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
            Tidak ada data siswa di kelas ini.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
