import 'package:flutter/material.dart';

import 'course_card.dart';

class TimeSlotSection extends StatefulWidget {
  final String timeSlot;

  TimeSlotSection({required this.timeSlot});

  @override
  _TimeSlotSectionState createState() => _TimeSlotSectionState();
}

class _TimeSlotSectionState extends State<TimeSlotSection> {
  List<Map<String, dynamic>> courses = [
    {
      'name': 'SEW',
      'totalSeats': 20,
      'seatsTaken': 0,
      'teacher': 'Prof.Vittori',
      'room': 'H930',
      'isSelected': false
    },
    {
      'name': 'SYT',
      'totalSeats': 20,
      'seatsTaken': 0,
      'teacher': 'Prof.Borko',
      'room': 'H927',
      'isSelected': false
    },
    {
      'name': 'MEDT',
      'totalSeats': 20,
      'seatsTaken': 0,
      'teacher': 'Prof.Schabel',
      'room': 'H928',
      'isSelected': false
    },
    // Add more courses as needed
  ];

  void _toggleCourse(int index) {
    setState(() {
      var course = courses[index];
      if (course['isSelected']) {
        course['seatsTaken'] = (course['seatsTaken'] as int) - 1;
      } else {
        course['seatsTaken'] = (course['seatsTaken'] as int) + 1;
      }
      course['isSelected'] = !course['isSelected'];
    });
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: Text('Time: ${widget.timeSlot}',
              style: Theme.of(context).textTheme.subtitle1),
        ),
        GridView.count(
          shrinkWrap: true,
          physics: NeverScrollableScrollPhysics(),
          crossAxisCount: 5,
          childAspectRatio: 2.5,
          children: List<Widget>.generate(courses.length, (index) {
            var course = courses[index];
            return CourseCard(
              courseName: course['name'],
              totalSeats: course['totalSeats'],
              seatsTaken: course['seatsTaken'],
              teacher: course['teacher'],
              room: course['room'],
              isSelected: course['isSelected'],
              onTap: () => _toggleCourse(index),
            );
          }),
        ),
      ],
    );
  }
}
