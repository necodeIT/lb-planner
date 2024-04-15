import 'package:flutter/material.dart';

class CourseCard extends StatelessWidget {
  final String courseName;
  final int totalSeats;
  final int seatsTaken;
  final String teacher;
  final String room;
  final bool isSelected;
  final VoidCallback onTap;

  CourseCard({
    required this.courseName,
    required this.totalSeats,
    required this.seatsTaken,
    required this.teacher,
    required this.room,
    required this.onTap,
    required this.isSelected,
  });

  @override
  Widget build(BuildContext context) {
    final seatsInfo = '$seatsTaken/$totalSeats';
    return Card(
      elevation: isSelected ? 4.0 : 0.0,
      color: isSelected ? Colors.blue.shade100 : Colors.white,
      child: ListTile(
        title: Text(courseName),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(Icons.event_seat, size: 20),
                SizedBox(width: 4),
                Text('Seats: $seatsInfo'),
              ],
            ),
            Row(
              children: [
                Icon(Icons.person_outline, size: 20),
                SizedBox(width: 4),
                Text('Teacher: $teacher'),
              ],
            ),
            Row(
              children: [
                Icon(Icons.meeting_room, size: 20),
                SizedBox(width: 4),
                Text('Room: $room'),
              ],
            ),
          ],
        ),
        trailing: Icon(
            isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
            color: isSelected ? Colors.green : null),
        onTap: onTap,
      ),
    );
  }
}
