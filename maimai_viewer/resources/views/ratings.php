<!DOCTYPE html>
<html>
    <head>
        <title>Songs List</title>
    </head>
    <body>
        <h1>Songs List</h1>

        <form method="GET" action="{{ route('songs.index') }}">
            <label for="difficulty">Difficulty:</label>
            <select name="difficulty" id="difficulty">
                <option value="">All</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
            <button type="submit">Filter</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Artist</th>
                    <th>Difficulty</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($songs as $song)
                    <tr>
                        <td>{{ $song->name }}</td>
                        <td>{{ $song->artist }}</td>
                        <td>{{ $song->difficulty }}</td>
                        <td>{{ $song->score }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
